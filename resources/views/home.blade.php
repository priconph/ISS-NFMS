@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">CSV Upload</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div id="divUploadCSV" style="float: left;">
                        <form id="formUploadCSV" method="post" enctype="multipart/form-data">
                            @csrf
                            Upload File : <input type="file" name="csv_file"> 
                            <button class="btn btn-primary btn-sm" type="submit" id="btnUploadCSV">Submit</button>
                        </form>

                    </div>
                    <div style="float: right;">
                        <span class="btn btn-warning btn-sm" style="background-color: yellow;" type="button">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Above 85% & Below 100% Usage &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="btn btn-danger btn-sm" style="background-color: red;" type="button">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 100% Usage
                    </div>

                    <br><br>
                    <div style="width: 100%;">
                        <div class="table-responsive">
                            <table class="table table-sm" style="width: 100%;" id="tblCSVData">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Path</th>
                                        <th>Size in GB</th>
                                        <th>Usage in GB</th>
                                        <th>Free in GB</th>
                                        <th>Percentage</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6">No Record Found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

        UploadCSV($("#formUploadCSV")[0]);

        $("#formUploadCSV").submit(function(event){
            event.preventDefault();
            UploadCSV(this);
        });
    });

    function UploadCSV(form){
        $.ajax({
            url: 'upload_csv',
            method: 'post',
            data: new FormData(form),
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){
                // alert('Loading...');
                let result = '<tr colspan="6">Loading...</tr>';
                $("#tblCSVData tbody").html(result);
            },
            success: function(JsonObject){
                let result = "";
                if(JsonObject['result'] == 1){
                    // console.log(JsonObject['data'].length);
                    for(let index = 0; index < JsonObject['data'].length; index++){
                        let free = parseFloat(JsonObject['data'][index]['size']) - parseFloat(JsonObject['data'][index]['usage']);
                        let percentage = parseFloat(JsonObject['data'][index]['usage']) / parseFloat(JsonObject['data'][index]['size']) * 100;

                        if(percentage >= 100){
                            result += '<tr style="background-color: red; color: white;">';
                        }
                        else if(percentage >= 85 && percentage < 100){
                            result += '<tr style="background-color: yellow; color: black;">';
                        }
                        else{
                            result += '<tr>';
                        }
                        result += '<td>' + JsonObject['data'][index]['description'] + '</td>';
                        result += '<td>' + JsonObject['data'][index]['path'] + '</td>';
                        result += '<td>' + JsonObject['data'][index]['size'] + '</td>';
                        result += '<td>' + JsonObject['data'][index]['usage'] + '</td>';
                        // result += '<td>' + JsonObject['data'][index]['free'].toFixed(2) + '</td>';
                        // result += '<td>' + percentage.toFixed(2) + '%</td>';
                        result += '<td>' + JsonObject['data'][index]['free'] + '</td>';
                        result += '<td>' + JsonObject['data'][index]['percentage'] + '%</td>';
                        result += '<td>' + JsonObject['data'][index]['size'] + ' GB / ' + JsonObject['data'][index]['free'] + ' GB Free ' + '</td>';
                        result += '</tr>';
                    }
                    $("#tblCSVData tbody").html(result);
                }
                else{
                    result = '<tr colspan="4">Importing Failed!</tr>';
                    $("#tblCSVData tbody").html(result);
                }
            },
            error: function(data, xhr, status){
                console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            }
        });
    }
</script>
@endsection
