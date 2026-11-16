<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ISS NFMS</title>

    <!-- Scripts -->
    <script src="{{ asset('public/js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('public/css/app.css') }}" rel="stylesheet">
    <style type="text/css">
        body, h1, h3 {
            font-family: Arial;
        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            font-size: 14px;
            font-family: Arial;
        }
    </style>
</head>
<body>
    <div id="app">
        <main class="py-4">
            <div class="container">
			    <div class="row justify-content-center">
			        <div class="col-md-12">
			            <div class="card">
			                <div class="card-header">
			                	<h1>ISS Network Folder Monitoring System</h1>
			                	<h3>{{ Carbon\Carbon::now()->isoFormat('lll') }}</h3>
			                </div>

				                <div class="card-body">
				                    <div style="float: right;">
				                        <span class="btn btn-warning btn-sm" style="background-color: yellow; border: 1px solid black;" type="button">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Above 85% Usage &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				                    </div>

				                    <br><br>
				                    <div style="width: 100%;">
				                        <div class="table-responsive">
				                            <table class="table table-sm" style="width: 100%;" id="tblCSVData">
				                                <thead>
				                                    <tr>
				                                        <th>Description</th>
				                                        <th>Status</th>
				                                        <th>Total Network Folder Size VS Free Disk Size</th>
				                                        <th>Percentage</th>
				                                    </tr>
				                                </thead>
				                                <tbody>
				                                	@if(count($data) > 0)
				                                		@for($index = 0; $index < count($data); $index++)
				                                			@if($data[$index]['percentage'] >= 85)	
				                                				<tr style="background-color: yellow; color: black;">
				                                			@else
				                                				<tr>
				                                			@endif()
						                                        <td>{{ $data[$index]['description'] }}</td>
						                                        @if($data[$index]['percentage'] >= 85)	
					                                				<td style="text-align: center;">NG</td>
					                                			@else
					                                				<td style="text-align: center;">OK</td>
					                                			@endif()
						                                        <td>{{ $data[$index]['size'] }}GB / {{ $data[$index]['free'] }}GB Free</td>
						                                        <td>{{ $data[$index]['percentage'] }}%</td>
						                                    </tr>
				                                		@endfor
				                                	@else
				                                    <tr>
				                                        <td colspan="4">No Record Found.</td>
				                                    </tr>
				                                	@endif
				                                </tbody>
				                            </table>
				                        </div>
				                    </div>
				                </div>
				            </div>
				        </div>
				    </div>
				</div>
        </main>
    </div>
</body>
</html>
