<?php
session_start();
if (!isset($_SESSION['auth']) || $_SESSION['auth'] != true) {
  header("Location: /login.php");
  exit();
}
?>

<html>
<head>
<title> Report </title>
<link rel="shortcut icon" type="image/png" href="https://cdn.iconscout.com/icon/free/png-256/red-among-us-3218512-2691060.png"/>
<link rel="stylesheet" type="text/css" href="style.css">
<script nonce="undefined" src="https://cdn.zingchart.com/zingchart.min.js"></script>
<script src="https://cdn.zinggrid.com/zinggrid.min.js" defer></script>
<style>
    html,
    body,
    #myChart {
      width: 100%;
      height: 100%;
    }
</style>
</head>
<body>
<h1> Generated Report on Mouse Clicks in Relation to the Homepage </h1>
<h3> How many users are having loading issues on the homepage? </h3>
<zing-grid src="https://jonchang.site/zing/performance"
    caption="Users' Load Times on Main Site"
    pager
    layout="card"
    page-size=12
    column-drag
    columns='[{ "index":"session_id"}, 
              { "index":"total_loadtime"}]'
  ></zing-grid>
  <br><br>

<div class="bigbox">
This table lists users based on their session ids, and their loadtimes. Based on this grid,
it can be seen that the overall loadtimes can range from around 80ms to over 2000 ms. This report
will take RAIL standards into consideration, such that a load time of less than 1000 ms is considered 
performant. To determine whether or not users are overall having loading issues on the homepage, a significant
proportion of users must have loadtimes of more than 1000ms. Due to the smaller dataset from a lack of visiting users
along with the fact that the data is literally all just me going to my homepage, it is not 
possible to truly discern whether or not most users are having loading issues. However, I am going to pretend that 
there is more than one person going to my homepage, and work under that assumption. For this report,
due to the smaller dataset, I will consider a proportion of more than 5% of users having a loadtime of more than 1000ms
as evidence pointing towards an optimization issue. 
</div>
<br>

<div id='myChart'></div>

<script>
    fetch("https://jonchang.site/zing/performance").then(
        data => { return data.json(); }
    ).then(        
        post =>  {
            var lowload = 0;
            var highload = 0;

            for (var i=0; i<post.length; i++) {
                var loadtime = post[i].total_loadtime; 

                if (loadtime < 1000) {
                    lowload += 1;
                } else {
                    highload += 1;
                }
            }

            var myConfig = {
            type: 'pie',
            legend: {
                header: { text: "Loading times",
                'font-family': "Georgia"},
                'background-color': "#ffe6e6",
                'border-width':2,
                'border-color': "red",
                'border-radius': "5px"
                
            },  
            labels : [
                {
                    text: "Measure of loading times across users.",
                    'font-family': "Georgia",'font-size':"26"
                }

            ],
            plot: {
                'background-color': "pink",
                'value-box': {
                placement: "out",
                'font-color': "gray",
                'font-size':12,
                'font-weight': "normal"
                }
            },
            series: [
                {
                values: [lowload],
                'background-color': "pink",
                text : "less than 1000ms",
                'font-family': "Georgia",
                'legend-marker': { 'background-color' : 'pink'},
                },
                {
                values: [highload],
                'background-color': "green",
                text : "1000ms+",
                'font-family': "Georgia",
                'legend-marker': { 'background-color' : 'green'},
                },
            ]
            };
            
            zingchart.render({
            id: 'myChart',
            data: myConfig,
            height: "80%",
            width: "75%"
            });
        });

  </script>
<div class="bigbox">
Based on this pie chart, it appears that only 4.7% of users who visit the homepage are suffering from long loadtimes. 
Since it falls under the specified threshold of 5%, it stands that there is no significant loading issues with the site.
However, as mentioned above, this is not a comprehensive examination of the homepage's loadtime, due to the low quality of the
data, along with certain outliers resulting from client-side issues (i.e. the data entry with a loadtime of 78629ms). Therefore, what can be concluded by
this report is that I can load my own site in a reasonable time around 95% of the time. So that's pretty cool I guess.
</div>
<br><br><br><br><br><br><br><br>
<footer>
Back to the
<a href="/home.php"> main </a>
site.
<br>
Or click 
<a href="/logout.php"> here </a>
to logout.
</footer>
</body>
</html>