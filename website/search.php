<!DOCTYPE html>
<?php
  require('util.php');
  $query = $_GET['q'];
  $con = get_con();
  $result = standard_search($query, $con);
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>IWantToStud.ty</title>
        <meta name="description" content="Search for professors by research interest">
        <meta name="author" content="Leah Alpert, Russell Cohen, Ram Bhaskar">
        <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]--><!-- Le styles -->
        <link rel="stylesheet" href="http://twitter.github.com/bootstrap/1.4.0/bootstrap.min.css">
        <link rel="stylesheet" href="my_css.css">
        <!-- Le fav and touch icons -->
        <link rel="shortcut icon" href="images/favicon.ico">
        <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
        <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
    </head>
    <body>
        <div class="topbar">
            <div class="topbar-inner">
                <div class="container-fluid">
                    <a class="brand" href="#">IWantToStud.ty</a>
                    <ul class="nav">
                        <li class="active">
                            <a href="#">Home</a>
                        </li>
                        <li>
                            <a href="#about">About</a>
                        </li>
                        <li>
                            <a href="#contact">Contact</a>
                        </li>
                    </ul>
                    <p class="pull-right">
                        Logged in as <a href="#">username</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="search_bar">
            <form class="bar_form" action="search.php">
                <label for="search" style="width:auto; padding-left:10px;">
                    Search
                </label>
                <div class="input" style="margin-left:65px;">
                    <input id="search" name="q" size="30" type="text" />&nbsp;<input type="submit" class="btn info" value="Go">
				</div>
            </form>
			
        </div>
        <div class="container-fluid">
            <div class="fixed_sidebar">
                <div class="well" style=" padding:0px;">
                    <form action="" class="form-stacked" style="padding:8px;">
                        <h5>Filter results by:</h5>
                        <hr style="margin:5px; padding:0px;">
                        <div class="clearfix">
                            <label id="university">
                            University
                            <div class="input" style="margin:0px; padding:0px;">
                                <ul class="inputs-list">
                                    <li>
                                        <label>
                                            <input type="checkbox" name="10" value="10" checked/><span>MIT (14)</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="checkbox" name="11" value="11" checked/><span>Stanford (8)</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="checkbox" name="12" value="12" checked/><span>CMU (5)</span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <hr style="margin:5px; padding:0px;">
                        <div class="clearfix">
                            <label id="department">
                                Department
                            </label>
                            <div class="input" style="margin:0px; padding:0px;">
                                <ul class="inputs-list">
                                    <li>
                                        <label>
                                            <input type="checkbox" name="20" value="20" checked/><span>Mechanical Engineering (22)</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="checkbox" name="21" value="21" checked/><span>Computer Science (9)</span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <hr style="margin:5px; padding:0px;">
                        <div class="clearfix">
                            <label id="uni2">
                                Universityyyy
                            </label>
                            <div class="input" style="margin:0px; padding:0px;">
                                <ul class="inputs-list">
                                    <li>
                                        <label>
                                            <input type="checkbox" name="30" value="30" checked/><span>MIT (14)</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="checkbox" name="31" value="31" checked/><span>Stanford (8)</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="checkbox" name="32" value="32" checked/><span>CMU (5)</span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="prof_content">
                <div class="hero-unit" style="padding:10px 10px 1px 15px; margin:0px 0px 15px 0px;">
                    <p>
                        <? echo mysql_num_rows($result); ?> professors researching <strong><?php echo $_GET['q']; ?></strong>
                    </p>
                </div>
                <ul class="media-grid prof_grid">
                    <?php
                      while($row = mysql_fetch_array($result)) {
                        include('prof_box.php');
                      } 
                    ?>
                </ul>
                <footer>
                    <p>
                        &copy; Company 2011
                    </p>
                </footer>
            </div>
        </div>
    </body>
</html>