<?php
session_start();
if (!isset($_SESSION['auth']) || $_SESSION['auth'] != true) {
  header("Location: /login.php");
  exit();
}
?>

<html>
<head>
<title> Reporting Dashboard </title>
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
<h1> Reporting Dashboard </h1>
<br>
<div class ="smallbox">
<h3>Click here to generate a report:</h3>
<a href="/loadtimes.php">Generate report</a>
<br>

<?php
if ($_SESSION["admin"]) {
  echo("<h3>Click here to manage users:</h3>
  <a href=\"/users.php\">User management</a>");
}
?>
</div>
<br>
<zing-grid src="https://jonchang.site/zing/performance"
    caption="Main Site Performance Metrics"
    pager
    page-size=10
    column-drag
    columns='[{ "index":"session_id"}, 
              { "index":"load_end"}, 
              { "index":"load_start"}, 
              { "index":"total_loadtime"}]'
  ></zing-grid>
  <br><br>
  <div id='myChart'></div>
  <div id='myChart2'></div>
  <script>
    fetch("https://jonchang.site/zing/activity").then(
        data => { return data.json(); }
    ).then(
        post =>  {
            var points = [];

            for (var i=0; i<post.length; i++) {
              if (post[i].mouse_click != "none") {
                points.push([post[i].mouse_x, post[i].mouse_y]);
              }
            }

            var myConfig = {
            type: 'scatter',
            backgroundColor: '#fff #fbfbfb',
            labels : [
                {
                    text: "Mouse positions on click.",
                    'font-family': "Georgia",'font-size':"26"
                }

            ],
            plot: {
              selectedMarker: {
                type: 'star5',
                backgroundColor: '#00a679',
                borderColor: '#00a679',
                borderWidth: '1px',
                size: '6px'
              },
              selectionMode: 'multiple'
            },
            scaleX: {
              label: {
                text: "X position"
              }
            },

            scaleY: {
              label: {
                text: "Y position"
              }
            },

            series: [
                { values: points,
                text : "points",
                tooltip: {
                  text: '%k / %v',
                  padding: '10px',
                  alpha: 0.8,
                  backgroundColor: '#FFF',
                  borderColor: '#4c77ba',
                  borderRadius: '8px',
                  borderWidth: '2px',
                  color: '#4c77ba',
                  textAlign: 'left'
                },
                },
            ]
            };
 
            zingchart.render({
            id: 'myChart',
            data: myConfig,
            height: "90%",
            width: "75%"
            });
        }
    );
  </script>
  <script>
    fetch("https://jonchang.site/zing/performance").then(
        data => { return data.json(); }
    ).then(        
        post =>  {
            var lowload = 0;
            var midload = 0;
            var highload = 0;
            var highestload = 0;

            for (var i=0; i<post.length; i++) {
                var loadtime = post[i].total_loadtime; 

                if (loadtime < 200) {
                    lowload += 1;
                } else if (loadtime < 400) {
                    midload += 1;
                } else if (loadtime < 600) {
                    highload += 1;
                } else {
                    highestload += 1;
                }
            }

            var myConfig = {
            type: 'bar', 
            labels : [
                {
                    text: "Count of users based on loading times.",
                    'font-family': "Georgia",'font-size':"26"
                }

            ],
            'scale-x': {
                labels: [ "0-199ms", "200-399ms", " 400-599ms", "600+ms"]
            },
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
                values: [lowload, midload, highload, highestload],
                'background-color': "pink",
                'font-family': "Georgia"
                }
            ]
            };
            
            zingchart.render({
            id: 'myChart2',
            data: myConfig,
            height: "80%",
            width: "50%"
            });
        });
</script>
<footer>
Click 
  <a href="/logout.php"> here </a>
to logout.
</footer>
</body>
</html>