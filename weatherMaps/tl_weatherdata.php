<?php
/* * *****************************************************************************************************************************************
 * File Name: weather_data_table.php
 * Project: Silicore
 * Description: File displays weather data in a dataTable and graph format for Production using Weather Underground API.
 * Notes:
 * =========================================================================================================================================
 * Change Log ([MM/DD/YYYY]|[Developer]|[Task Ticket] - [Description]
 * =========================================================================================================================================
 * 06/20/2018|gndede|KACE:23050 - Initial creation
 * 06/20/2018|gndede|KACE:23050 - Adjusted necessary fields to get/set data for Tolar location.
 * **************************************************************************************************************************************** */

    require_once('/var/www/sites/silicore/Includes/security.php');
	require_once ('/var/www/sites/silicore/Includes/Security/dbaccess.php');
?>

<?php
	$json_string = file_get_contents("../../Includes/Development/tl_weatherdata.json");
	$parsed_json = json_decode($json_string, true);
?>

<?php
   //open connection to mysql db
   $dbc = databaseConnectionInfo();
   
   $connection = mysqli_connect($dbc['silicore_hostname'], $dbc['silicore_username'], $dbc['silicore_pwd'], $dbc['silicore_dbname']) or die("Error " . mysqli_error($connection));

   //fetch table rows from mysql db
   $sql = "CALL sp_tl_plc_RainfallSummaryGet";
	$result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));
	$chart_data = '';
	while($row = mysqli_fetch_array($result))
	{
	$chart_data .= "{    Date:'".$row["date_data"]."',
						Rainfall:".$row["Rainfall"].",
					   Wind:".$row["wind"].",
					   AverageHighTemp:".$row["avg_high_temp"].", 
						AverageLowTemp:".$row["avg_low_temp"]."}, ";
	}
	$chart_values = substr($chart_data, 0, -2);
  
  //echo "<pre>";
  //var_dump($chart_values);
  //echo "</pre>";
?>

<!DOCTYPE html>
<html>
      <head>
            <title>Rainfall, Wind & Temperature | Vista Sand</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
            <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"</script>
            <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
            <link rel="stylesheet" type="text/css" href="../../Content/Development/dataTables.bootstrap.min.css">
            <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/f3e99a6c02/integration/bootstrap/3/dataTables.bootstrap.js"></script>
     
            
            <script>
                    $(document).ready(function() 
                    {
                      $('#weatherDataTable').DataTable(); // $('#weatherDataTable').DataTable();
                                                        // $('#weatherDataTable').DataTable({bFilter: false}); // Removes search bar
                                                        // $('#weatherDataTable').DataTable({bFilter: false, bInfo: false}); Removes search bar and count of entries display.
                                                        // $('#weatherDataTable').DataTable({bFilter: false, bInfo: false, bLengthChange: false}); Removes search bar, count of entries display, and showing # of entries dropdown.
                                                        // Further documentation notes, see... https://datatables.net/
                    });
            </script>
            
            <!-- Tabbed Content with jQuery and CSS: https://codepen.io/cssjockey/pen/jGzuK-->
            <!--<script>
                    $(document).ready(function(){
                            $('ul.tab-names li').click(function(){
                              var tab_id = $(this).attr('data-tab');

                              $('ul.tab-names li').removeClass('current');
                              $('.tab-content').removeClass('current');

                              $(this).addClass('current');
                              $("#"+tab_id).addClass('current');
                            });
                    });
            </script>-->
            
            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
            <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">
            <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
            
            <style>
                  .header_text {
                    font-family: arial,helvetica,sans-serif;
                    font-size: 13px;
                    float: left;
                    postition: relative;
                    bottom: 4px;
                    width: 100%;
                  }

                  .navLI
                  {
                      font-size: 16px;
                  }

                  .sign_in_block
                  {
                    font-size: 16px;
                  }
                  
                   .usertable 
                  {
                    overflow: hidden;
                  }
            </style>
        
           <!--<style>
                    body{
                      margin-top: 100px;
                      font-family: 'Trebuchet MS', serif;
                      line-height: 1.6
                    }
                    .tabs{
                      width: 800px;
                      margin: 0 auto;
                    }
                    
                    ul.tab-names{
                      margin: 0px;
                      padding: 0px;
                      list-style: none;
                    }
                    ul.tab-names li{
                      background: none;
                      color: #222;
                      //display: inline-block;
                      padding: 10px 15px;
                      cursor: pointer;
                    }

                    ul.tab-names li.current{
                      background: #ededed;
                      color: #222;
                    }

                    .tab-content{
                      display: none;
                      //background: #ededed;
                      padding: 15px;
                    }

                    .tab-content.current{
                      display: inherit;
                    }
            </style-->
      </head>

      <body>
            <div class="tabs">
                      <!--<ul class="tab-names">
                              <li class="tab-link current" data-tab="tab-1"><a href="#tabs-1">Weather Table</a></li>
                              <li class="tab-link" data-tab="tab-2"><a href="#tabs-2">Weather Graph</a></li>
                      </ul>-->

                      <div id="tab-1" class="tab-content current">
                              <div class="header" style="width:900px;">
                                    <h2 align="center">Rainfall, Wind & Temperature | Vista Sand</h2>
                                    <h3 align="center">Tolar, Texas</h3>  
                                    <br /><br /><hr />
                              </div>
                          
                            <div class="usertable">
                              <table id="weatherDataTable" class="table table-striped table-bordered" style="width:100%;">
                              <h4>Table</h4>
                                    <thead>
                                      <tr>
                                        <th>Date</th>
                                        <th>Rainfall</th>
                                        <th>Wind</th>
                                        <th>High Temp</th>
                                        <th>Low Temp</th>
                                      </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                      foreach ($parsed_json as $item)
                                      {
                                        $date = new datetime($item['date']);
                                        $date = $date->format('Y-m-d');
                                        echo "<tr>"
                                            //."<td>". $item['id'] ."</td>"
                                            ."<td>". $date ."</td>"
                                            ."<td>". $item['rainfall'] ."</td>"
                                            ."<td>". $item['wind'] ."</td>"
                                            ."<td>". $item['high_temp'] ."</td>"
                                            ."<td>". $item['low_temp'] ."</td>"
                                            ."</tr>";
                                      };
                                    ?>
                                    </tbody>

                                    <tfoot>
                                    </tfoot>
                              </table><br /><br /><br />
                      </div>
                      </div>

                      <div id="tab-2" class="tab-content">
                              <div class="container" style="width:900px;">
                              <h4>Graph</h4>
                              <p>*Note: Mouse-over a dotted time period to view monthly calculated results.</p>
                                      <br />
                                      <div id="chart"></div>
                              </div>
                        
                              <script>
                                        Morris.Line({
                                        element : 'chart',
                                        data:[<?php echo $chart_values; ?>],
                                        xkey:'Date',
                                        ykeys:['Rainfall', 'Wind', 'AverageHighTemp','AverageLowTemp'],
                                        labels:['(Monthly Sum) Rainfall', '(Monthly Avg.) Wind', '(Monthly Avg.) High Temperature','(Monthly Avg.) Low Temperature'],
                                        hideHover:'auto'
                                        //stacked:true
                                        });
                              </script>
                      </div>
            </div>
      </body>                 
</html>