<?php
    session_start(); //Syntax to start the session

    if(!isset($_SESSION['vlabuser'])) {
        $_SESSION['vlabuser'] = uniqid(md5(microtime ()), true);  //Check for Session or Create new session
    }

    $ferror = '';
    if(isset($_FILES["verilogfile"])) {   					 // Syntax to Upload a file 
        $allowedExts = array("v");  						// Only Verilog (.v) files allowed | it is stored in an array 
        $temp = explode(".", $_FILES["verilogfile"]["name"]); 			// Seperates extension from the file name
        $extension = end($temp);						// extension becomes temp
        if(in_array($extension, $allowedExts)) { 				 // If the uploaded file is in the allowed extensions 
            if ($_FILES["verilogfile"]["error"] > 0) {				 //Check for upload errors ex.Connectivity issues
                $ferror =  "Error: " . $_FILES["verilogfile"]["error"];
            } else {
                if(!file_exists('sessions/'.$_SESSION['vlabuser'])) { 		//If session directory is not there , It creates a session directory
                    mkdir('sessions/'.$_SESSION['vlabuser']);
                }
                if(!file_exists('sessions/'.$_SESSION['vlabuser'].'/temp')) {	 //If session directory is not there , It creates a session directory
                    mkdir('sessions/'.$_SESSION['vlabuser'].'/temp');
                }
                if(move_uploaded_file($_FILES["verilogfile"]["tmp_name"], 'sessions/'.$_SESSION['vlabuser'].'/uploader_dut.v')) {
                    #$ferror = 'runscript';  //Move the uploaded file to the session directory | Name it as uploader_dut.v | Only if the file is copied will runscript be initialized or error message is displayed
                    $ferror = 'radiobuttons';
                } else {
                    $ferror = 'Error occured while moving uploaded file to session directory.';
                }
            }
        } else {
            $ferror = 'Invalid file extension!';				 //Print the error message
        }

	
    }elseif(isset($_POST['pins']) && isset($_POST['csvalue'])) {
	$ferror = 'pininputs';
    }elseif(isset($_POST['pininputform'])) {
	$ferror = 'runscript';
    }

?>

<html>
    <head>
        <title>Virtual FPGA Simulation Laboratory</title>
	<link rel="stylesheet" href="includes/css/bootstrap.min.css">
    </head>

    <body>
       <div class="navbar navbar-default navbar-fixed-top"><div class="container">
            <div class="navbar-header">
                <a href="../" class="navbar-brand">Experiments</a>
                <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="navbar-collapse collapse" id="navbar-main">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="themes">Upcounter <span class="caret"></span></a>
                        <ul class="dropdown-menu" aria-labelledby="themes">
                            <li><a href="../docs/Expt1_bidirectional_counter/updown_counter.v">Verilog</a></li>
                            <li class="divider"></li>
                            <li><a href="../docs/Expt1_bidirectional_counter/theory.pdf">Theory</a></li>
                            <li><a href="../docs/Expt1_bidirectional_counter/tutorial.pdf">Tutorial</a></li>
                            <li><a href="../docs/Expt1_bidirectional_counter/procedure.pdf">Procedure</a></li>
                        </ul>
                    </li>
                    <li><a href="https://www.ee.iitb.ac.in/vlabsfpga/">RT FPGA based Automated System </a></li>
                    <li><a href="https://www.ee.iitb.ac.in/~hpc/">HPC Laboratory</a></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="download">Download <span class="caret"></span></a>
                        <ul class="dropdown-menu" aria-labelledby="download">
                            <li><a href="http://www.eecg.utoronto.ca/vtr/terms.html">VTR Tool</a></li>
                           <!-- <li><a href="./bootstrap.css">bootstrap.css</a></li>
                            <li class="divider"></li>
                            <li><a href="./variables.less">variables.less</a></li>
                            <li><a href="./bootswatch.less">bootswatch.less</a></li> -->
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                  <!--  <li><a href="https: //www.ee.iitb.ac.in/wiki/faculty/patkar"target="_blank">Sachin Patkar</a></li> -->
                  <!--   <li><a href="https://www.google.co.in" target="_blank">David Dhas</a></li> -->
                </ul>
            </div>
        </div></div>
	 <div id="3" style="display:none;" class="actdivs">
                <div class="stephd"> Provide input vectors &amp; Execute Design</div>
                <div id="inpvecgui" tabindex="300"></div><div id="inpvecguiicon">[-]</div>
         </div>
        <div class="container" style="margin-top: 85px;">
            <div class="bs-docs-section">
                <div class="row">
                    <div class="col-lg-12">
                        <form method="post" enctype="multipart/form-data" class="form-horizontal">
                            <fieldset>
                                <legend>Synthesis and Simulation through ODIN</legend>
                                <div class="form-group">
                                    <label for="verilogfile" class="col-lg-2 control-label">Upload verilog file here:</label>
                                    <div class="col-lg-10">
                                        <input class="btn btn-default" id="verilogfile" type="file" name="verilogfile">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-10 col-lg-offset-2">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
            <?php
                if($ferror){
                   if($ferror == 'radiobuttons'){ 
			echo '<h4>Check Verilog Syntax</h4>';
                        echo '<p style="margin: 10px; text-align:justify;"><pre>';
                        passthru('/usr/bin/perl /var/www/html/vlabs/david_checker.pl sessions/'.$_SESSION['vlabuser'].'/uploader_dut.v');
                        echo '</pre></p>';
			$csvalue = trim(exec('./return_commas.pl sessions/'.$_SESSION['vlabuser'].'/uploader_dut.v'));
			$input_variables = explode(",", $csvalue);
			if(sizeof($input_variables)) {
			    echo '<form method="POST" class="btn btn-default"><h4>Select Input Clock:</h4> &nbsp;&nbsp;&nbsp;';
			    echo '<input type="hidden" name="csvalue" value="'.$csvalue.'">';
			    $flag = 1;
			    if(in_array('clk', $input_variables))
			    	$flag = 2;
			    foreach($input_variables as $input_variable) {
				if($flag == 1) {
			    	    echo '<input type="radio" name="pins" value="'.$input_variable.'" checked> '.$input_variable.' &nbsp;&nbsp;&nbsp;';
				    $flag = 0;
				}elseif($flag == 2 and $input_variable == 'clk') {
			    	    echo '<input type="radio" name="pins" value="'.$input_variable.'" checked> '.$input_variable.' &nbsp;&nbsp;&nbsp;';
				    $flag = 0;
				} else {
			    	    echo '<input type="radio" name="pins" value="'.$input_variable.'"> '.$input_variable.' &nbsp;&nbsp;&nbsp;';
				}
			    }
			    echo '<br /><button type="submit" style="margin-top: 10px;" class="btn btn-success">Submit</button>';
			    echo '</form>';
			}
                   }elseif($ferror == 'pininputs'){
			$input_variables = explode(",", $_POST['csvalue']);
			if(sizeof($input_variables)) {
                            echo '<h4>Input Test Vectors:</h4>';
			    echo '<form method="POST" class="btn btn-default">
					<table class="table table-bordered table-hover"><tr>';
			    $pinsavailable = '';
			    foreach($input_variables as $input_variable) {
				if($input_variable != $_POST['pins'] && $input_variable != 'clk'){
				    echo '<th style="text-align: center; font-size: 16px;">'.$input_variable.'</th>';
				    if($pinsavailable)
					$pinsavailable .= ',';
				    $pinsavailable .= $input_variable;
				}
			    }
			    echo '</tr>';
			    for($i = 0; $i < 5; $i++) {
				echo '<tr>';
			    	foreach($input_variables as $input_variable) {
				    if($input_variable != $_POST['pins'] && $input_variable != 'clk')
				    	echo '<td><input type="text" name="'.$input_variable.$i.'" class="form-control"></td>';
			    	}
				echo '</tr>';
			    }
			    echo '</table><input type="hidden" name="pininputform" value="'.$pinsavailable.'">';
			    echo '<button type="submit" style="margin-top: 10px;" class="btn btn-success">Submit</button></form>';
			}
                   }elseif($ferror == 'runscript'){
			$handle = fopen('/var/www/html/vlabs/sessions/'.$_SESSION['vlabuser'].'/input_vectors.txt', 'w');
			$input_variables = explode(",", $_POST['pininputform']);
			foreach($input_variables as $input_variable){
			    fwrite($handle, $input_variable.' ');
			}
			for($i = 0; $i < 5; $i++) {
			    fwrite($handle, "\n");
			    foreach($input_variables as $input_variable) {
				#echo $i;
				if(isset($_POST[$input_variable.$i]) && $_POST[$input_variable.$i]){
				    fwrite($handle, $_POST[$input_variable.$i].' ');
				} else {
				    fwrite($handle, '0 ');
				}
			    }
			}
			fclose($handle);
                        echo '<h4>Result:</h4>';
			if(file_exists('/var/www/html/vlabs/sessions/'.$_SESSION['vlabuser'].'/output.blif')) {
				unlink('/var/www/html/vlabs/sessions/'.$_SESSION['vlabuser'].'/output.blif');
			}
			$currentdir = getcwd();
			chdir('../vtr_release/ODIN_II/');
                        echo '<p style="margin: 10px; text-align:justify;"><pre>';
                        passthru('./odin_II.exe -t /var/www/html/vlabs/sessions/'.$_SESSION['vlabuser'].'/input_vectors.txt -V /var/www/html/vlabs/sessions/'.$_SESSION['vlabuser'].'/uploader_dut.v -o /var/www/html/vlabs/sessions/'.$_SESSION['vlabuser'].'/output.blif');
			echo '</pre></p>';
			$srcdir = getcwd();
                     	copy($srcdir.'/output_vectors','/var/www/html/vlabs/sessions/'.$_SESSION['vlabuser'].'/output_vectors.txt');
			chdir($currentdir);
			if(file_exists('/var/www/html/vlabs/sessions/'.$_SESSION['vlabuser'].'/output.blif')) {
				echo '<p><a href="sessions/'.$_SESSION['vlabuser'].'/output.blif">Click here</a> to download output blif file</p>';
	                	#echo '<p><a href="/var/www/html/vtr_release/ODIN_II/sample_arch.xml">Click here</a> to download the input vector file.</p>';
				echo '<p><a href="sessions/'.$_SESSION['vlabuser'].'/output_vectors.txt">Click here</a> to download output vectors</p>';
                 	}
                    }else {
                        echo '<h4>Error:</h4>';
                        echo '<p style="margin: 10px; text-align:justify;">'.$ferror.'</p>';
                    }
                }
            ?>
	</div></div></div></div>
	<script src="includes/js/jquery-1.11.1.min.js"></script>
	<script src="includes/js/bootstrap.min.js"></script>
    </body>
</html>
