<?php require_once('header.php'); ?>

<div class="container justify-content-center">
    <h2 class="text-center">Favorite Repository</h2>
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
    $(document).ready(function() {
        $.ajax({
            type: "GET",
            datatype: "json",
            url: "<?= base_url();?>/repository/get_favorite_repository",
            success: function (result) {
                // console.log(result.length);
                response= JSON.parse(result);
                for (var i = 0; i < response.length ; i++) {
                    let avatarUrl = response[i]['avatar_url'];
                    let repositoryName = response[i]['name'];
                    let userName = response[i]['login'];
                    let newRowContent = "<tr><td><img src='"+avatarUrl+"' width='30' height='30'></td><td>"+repositoryName+"</td><td></td><td>"+userName+"</td></tr>";
                    $("#example tbody").append(newRowContent);
                }
                $('#example').DataTable();
            }
        });
    } );
</script>


