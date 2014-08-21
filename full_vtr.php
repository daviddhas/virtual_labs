<?php
    session_start(); //Syntax to start the session

    if(!isset($_SESSION['vlabuser'])) { //Check for Session or Create new session
        $_SESSION['vlabuser'] = uniqid(md5(microtime ()), true);  
    }

    $ferror = '';
    if(isset($_FILES["verilogfile"])) {   				 //Syntax to Upload a file 
        $allowedExts = array("v"); 					 // Only Verilog (.v) files allowed | it is stored in an array 
        $temp = explode(".", $_FILES["verilogfile"]["name"]); 		// Seperates extension from the file name
        $extension = end($temp);					// extension becomes temp
        if(in_array($extension, $allowedExts)) {  			// If the uploaded file is in the allowed extensions 
            if ($_FILES["verilogfile"]["error"] > 0) { 			//Check for upload errors ex.Connectivity issues
                $ferror =  "Error: " . $_FILES["verilogfile"]["error"];
            } else {
                if(!file_exists('sessions/'.$_SESSION['vlabuser'])) { //If session directory is not there , It creates a session directory
                    mkdir('sessions/'.$_SESSION['vlabuser']);
                }
		 if(!file_exists('sessions/'.$_SESSION['vlabuser'].'/temp')) { //If session directory is not there , It creates a session directory
                    mkdir('sessions/'.$_SESSION['vlabuser'].'/temp');
                }

                if(move_uploaded_file($_FILES["verilogfile"]["tmp_name"], 'sessions/'.$_SESSION['vlabuser'].'/uploader_dut.v')) {
                    $ferror = 'runscript';  				//Move the uploaded file to the session directory | Name it as uploader_dut.v | Only if the file is copied will runscript be initialized or error message is displayed
                } else {
                    $ferror = 'Error occured while moving uploaded file to session directory.';
                }
            }
        } else {
            $ferror = 'Invalid file extension!'; 			//Print the error message
        }
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
                    <li><a href="https://www.google.co.in" target="_blank">David Dhas</a></li> 
                </ul>
            </div>
        </div></div>
        <div class="container" style="margin-top: 85px;">
            <div class="bs-docs-section">
                <div class="row">
                    <div class="col-lg-12">
                        <form method="post" enctype="multipart/form-data" class="form-horizontal">
                            <fieldset>
                                <legend> Entire  VTR Tool Flow</legend>
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
	 <div id="3" style="display:none;" class="actdivs">
                <div class="stephd">Step 3: Provide input vectors &amp; Execute Design</div>
                <div id="inpvecgui" tabindex="300"></div><div id="inpvecguiicon">[-]</div>
         </div>

                            </fieldset>
            <?php
                if($ferror){
                    if($ferror == 'runscript'){
                        echo '<h4>Result:</h4>';
                        echo '<p style="margin: 10px; text-align:justify;"><pre>';
			if(file_exists('/var/www/html/vlabs/sessions/'.$_SESSION['vlabuser'].'/output.blif')) {
				unlink('/var/www/html/vlabs/sessions/'.$_SESSION['vlabuser'].'/output.blif');
			}
			$currentdir = getcwd();
			chdir('../vtr_release/vtr_flow/');
                        passthru('perl scripts/run_vtr_flow.pl /var/www/html/vlabs/sessions/'.$_SESSION['vlabuser'].'/uploader_dut.v /var/www/html/vtr_release/vtr_flow/sample_arch.xml -temp_dir /var/www/html/vlabs/sessions/'.$_SESSION['vlabuser']."/temp/");
			
                        echo '</pre></p>';
			chdir($currentdir);
			if(file_exists('/var/www/html/vlabs/sessions/'.$_SESSION['vlabuser'].'/output.blif')) {
				echo '<p><a href="sessions/'.$_SESSION['vlabuser'].'/output.blif">Click here</a> to download output blif file.</p>';
	                	echo '<p><a href="/var/www/html/vtr_release/ODIN_II/sample_arch.xml">Click here</a> to download the input vector file.</p>';
                                 echo '<p><a href="../vtr_release/ODIN_II/output_vectors">Click here</a> to download the output vector file.</p>';
                 	}
                    }else {
                        echo '<h4>Error:</h4>';
                        echo '<p style="margin: 10px; text-align:justify;">'.$ferror.'</p>';
                    } 
                }  
            ?>  
        </form>
	</div></div></div></div>
	<script src="includes/js/jquery-1.11.1.min.js"></script>
	<script src="includes/js/bootstrap.min.js"></script>
    </body>
</html>
