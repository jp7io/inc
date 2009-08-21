<?php

/************************************************************************/
/* PHPCaptcha.class.php                                                 */
/* =====================================================================*/
/* @Copyright (c) 2005 by Gobinath, All Rights Reserved                 */
/*                                                                      */ 
/* @Author(s): Gobinath (gobinathm at gmail dot com)                    */
/*                                                                      */
/* @Version: 1.0.0     @Version Date: May 2nd, 2005                     */
/*                                                                      */
/* @Package: PHPCaptcha      (Title:  Captcha on PHP)                   */
/* =====================================================================*/ 
/* @Purpose:                                                            */ 
/* Implementing Gimpy Captcha System in PHP                             */
/*                                                                      */
/* @Reason: For Avoiding Automated Registration                         */
/************************************************************************/
/*                                                                      */
/*   * GNU General Public License (Version 2, June 1991)                */
/*   *                                                                  */
/*   * This program is free software; you can redistribute              */
/*   * it and/or modify it under the terms of the GNU                   */
/*   * General Public License as published by the Free                  */
/*   * Software Foundation; either version 2 of the License,            */
/*   * or (at your option) any later version.                           */
/*   *                                                                  */  
/*   * This program is distributed in the hope that it will             */
/*   * be useful, but WITHOUT ANY WARRANTY; without even the            */
/*   * implied warranty of MERCHANTABILITY or FITNESS FOR A             */
/*   * PARTICULAR PURPOSE. See the GNU General Public License           */
/*   * for more details. 																*/
/*                                                                      */
/* Note: CAPTCHA is a trademark of Carnegie Mellon University.          */
/*                                                                      */
/*  The CAPTCHA Project is a project of the School of Computer Science  */
/* at Carnegie Mellon University. It is funded by the NSF Aladdin Center*/
/*                                                                      */
/************************************************************************/
/*                                                                      */
/* View the Resource.txt file for More Information Related to the Class */
/*                                                                      */
/************************************************************************/

error_reporting(ALL); // Sets the Error Reporting Level To ALL
// Here we are using the Random Password class for generating random strings 
include('rndPass.class.php');

class PHPCaptcha Extends rndPass{

    //@Font Base Directory
    var $fontDir;
    
    //@Font List Array Variable
    var $fonts;
    
    //@Variable to hold the Font filename which will be used in image.
    var $FontUse;
    
    //Captcha String Variable
    var $CaptchaString;
    
    function PHPCaptcha($dir,$Type=NULL,$Len=NULL,$str=NULL){
    // Paramenter TYPE is to specify the TYPE of Captcha String
    // TYPE can be 'rnd','gnl','usr'. If you want to Use rnd then u have to pass a parameter Length. 
    // For other the Parameter is not Required 
    // By Default it will Take gnl. gnl will read a available text file and generates Captcha Images. 
    // str is required only when the type is usr  
        $this->fonts = array();  //Initilize the variable as an array;
        $this->fontDir = $dir; // Assigning the font Directory;
        $this->Font2Use();  //Call to the Function, Which Give the RGB Values of the Hex Color Code
        //echo $Type;
        if($Type == 'usr'){
			  //Code for user Defined 
        } 
        if($Type == 'rnd'){
	          $this->GetRNDstring($Len);
	          //echo "This";
	     }
	     else{
	           $this->CaptchaString = "test";
	     }
        //echo $this->FontUse;
        //trigger_error("Cannot divide by zero", E_USER_ERROR);
         $_SESSION['CAPTCHAString'] = $this->CaptchaString;
	     $this->CaptchaImage();
    } 
    
    function CaptchaImage(){  
       ob_start();
       header("Content-type:image/png"); 
       $imgname ='images/comet1.png';
       $im = imagecreatefrompng($imgname);
       $black = imagecolorallocate($im, 255, 17, 4);  
       $white = imagecolorallocate($im, 255, 255, 255);
       $font = imageloadfont('font/'.$this->FontUse); 
       imagestring($im, $font, 5, 5,$this->CaptchaString, $black);
       imagepng($im);    
    }    
    
    function GetCaptchaString(){
       //Function Which Will Return the Captcha String In Encrypted Format
       return md5($this->CaptchaString);
    }
    
    //Functions Gets the List of GDF type Fonts available in the Given Directory  
    function GetFonts(){
       $tmpDir = opendir($this->fontDir);  // Opening the Font Directory 
       while ($tmpfile=readdir($tmpDir)) {
          if(!(is_dir($tmpfile))){
              $fileNameLen = strlen($tmpfile);    // Get the Length of the Filename 
              $fileext = substr($tmpfile,($fileNameLen-3),3); //Seperate the File Extenstion  
              if($fileext == "gdf"){
                  array_push($this->fonts ,$tmpfile);
              }
          }
       }
    }
    
    function Font2Use(){ 
       $this->GetFonts();
       $fontNo = rand(0,count($this->fonts)); 
       $fontName = $this->fonts[$fontNo];
       if ($fontName == ""){
          $this->FontUse =$this->fonts[0];
       }
       else{ 
          $this->FontUse= $fontName; 
       }    
       return $this->FontUse;
    }   
    
    
    function GetRNDstring($Len){
      $this->rndPass($Len);
      $this->CaptchaString = $this->PassGen();
    } 
     
}
?>