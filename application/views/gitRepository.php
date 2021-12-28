<?php require_once('header.php'); ?>

<div class="container justify-content-center">
    <h2 class="text-center">Git  Repository</h2>
    <button type="button" class="btn btn-primary saveRepository">Save</button><br>
    <div class="row">
        <div class="col-md-12">
            <table id="example" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>User/Repo Image</th>
                        <th>Repo Name</th>
                        <th>Reputation Of The Repo</th>
                        <th>User Name</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        
    </div>
</div>

<script>

    // Pipelining function for DataTables. To be used to the `ajax` option of DataTables
    //
    $.fn.dataTable.pipeline = function(opts) {
        // Configuration options
        var conf = $.extend({
            pages: 5, // number of pages to cache
            url: '', // script url
            data: null, // function or object with parameters to send to the server
            // matching how `ajax.data` works in DataTables
            method: 'GET' // Ajax HTTP method
        }, opts);

        // Private variables for storing the cache
        var cacheLower = -1;
        var cacheUpper = null;
        var cacheLastRequest = null;
        var cacheLastJson = null;

        return function(request, drawCallback, settings) {
            var ajax = false;
            var requestStart = request.start;
            var drawStart = request.start;
            var requestLength = request.length;
            var requestEnd = requestStart + requestLength;

            if (settings.clearCache) {
                // API requested that the cache be cleared
                ajax = true;
                settings.clearCache = false;
            } else if (cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper) {
                // outside cached data - need to make a request
                ajax = true;
            } else if (JSON.stringify(request.order) !== JSON.stringify(cacheLastRequest.order) ||
                JSON.stringify(request.columns) !== JSON.stringify(cacheLastRequest.columns) ||
                JSON.stringify(request.search) !== JSON.stringify(cacheLastRequest.search)
            ) {
                // properties changed (ordering, columns, searching)
                ajax = true;
            }

            // Store the request for checking next time around
            cacheLastRequest = $.extend(true, {}, request);

            if (ajax) {
                // Need data from the server
                if (requestStart < cacheLower) {
                    requestStart = requestStart - (requestLength * (conf.pages - 1));

                    if (requestStart < 0) {
                        requestStart = 0;
                    }
                }

                cacheLower = requestStart;
                cacheUpper = requestStart + (requestLength * conf.pages);

                request.start = requestStart;
                request.length = requestLength * conf.pages;

                // Provide the same `data` options as DataTables.
                if (typeof conf.data === 'function') {
                    // As a function it is executed with the data object as an arg
                    // for manipulation. If an object is returned, it is used as the
                    // data object to submit
                    var d = conf.data(request);
                    if (d) {
                        $.extend(request, d);
                    }
                } else if ($.isPlainObject(conf.data)) {
                    // As an object, the data given extends the default
                    $.extend(request, conf.data);
                }

                return $.ajax({
                    "type": conf.method,
                    "url": conf.url,
                    "data": request,
                    "dataType": "json",
                    "cache": false,
                    "success": function(json) {
                        cacheLastJson = $.extend(true, {}, json);

                        if (cacheLower != drawStart) {
                            json.data.splice(0, drawStart - cacheLower);
                        }
                        if (requestLength >= -1) {
                            json.data.splice(requestLength, json.data.length);
                        }

                        drawCallback(json);
                    }
                });
            } else {
                json = $.extend(true, {}, cacheLastJson);
                json.draw = request.draw; // Update the echo for each response
                json.data.splice(0, requestStart - cacheLower);
                json.data.splice(requestLength, json.data.length);

                drawCallback(json);
            }
        }
    };

    // Register an API method that will empty the pipelined data, forcing an Ajax
    // fetch on the next draw (i.e. `table.clearPipeline().draw()`)
    $.fn.dataTable.Api.register('clearPipeline()', function() {
        return this.iterator('table', function(settings) {
            settings.clearCache = true;
        });
    });


    $(document).ready(function() {

        // DataTables initialisation
        $('#example').DataTable({
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "pageLength": 20,
            "oSearch": {"sSearch": 'ios'},
            dom: 'Bfrtip',                            
            buttons: [
            'copy', 'excel', 'pdf', 'print'
            ],
            "order": [
                [2, "desc"]
            ], // Default Order BY
            "ajax": $.fn.dataTable.pipeline({
                url: "<?= base_url();?>/repository/getRepository",
                pages: 1, // number of pages to cache
            }),
            "columns": [{
                    "data": "null",
                    "render":function(data, type, row, meta){
                        let avatarUrl = row['owner']['avatar_url'];
                        return "<img src='"+avatarUrl+"' width='25' height='25'>"
                    }
                },
                {
                    "data": "name"
                },
                {
                    "data": "stargazers_count"
                },
                {
                     "data": "null",
                     "render": function(data, type, row, meta){
                        return row['owner']['login'];
                    }
                }
            ],
            "columnDefs": [ {
                'targets': [0,3], /* column index */
                'orderable': false, /* true or false */
             }]
        });


        //Save Data 
        $(document).on("click", ".saveRepository", function () {
            console.log("click");
            var DataTableList = [];
            var table = $('#example').DataTable();
            table.rows().every( function () {
                var d = this.data();
                DataTableList.push(d);
            });

            add(DataTableList);
        });

        function add(data){
             /* Add data */
            var data_to_send = JSON.stringify({
                data
            });

            $.ajax({
                url: "<?= base_url();?>repository/addRepository",
                type: "POST",
                data:data_to_send,
                contentType: false,
                cache: false,
                processData: false,
                success: function(JSON) {
                     alert(JSON);
                }
            });
        }


    });

    // "https://api.github.com/search/repositories?q=php&sort=stars&order=desc&page=1&per_page=100"
</script>


