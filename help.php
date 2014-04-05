<?php
   require_once "shape.php";
   
   //the styling of the help page
   $html = sass (' 
      $red : #f00;
      $font: Verdana;
      $fontcolor: #999999;
      $fontsize: 14px;
      $outputbgcolor : red;
      $codebgcolor: #f3f3f3;
      $navbgcolor: yellow;
      
      * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
      }
      
      body {
        font-family : $font;
        color : $fontcolor;
        font-size: $fontsize;  
      }
      
      nav {
        display: block;
        a {
          padding: 0.5em;
          text-decoration: none;
          background-color: $navbgcolor;
          border: 1px solid $fontcolor;
          margin: 0.2em;
        }      
        margin : 0.2em;
      }
      
      h2 {
        margin-bottom : 0.5em;
      }
   
      article {
        border : 1px dashed $fontcolor;
        padding: 0.5em;
        margin: 0.5em;
      }
   
      .codeexample {
        h2 {
          text-decoration: underline;
        }
        
        p {
          background-color: $codebgcolor;
          padding: 0.5em;
        }
        
        padding : 1em;
      }
      
      .codeoutput {
        h2 {
          text-decoration: underline;
        }
        p {
          background: $outputbgcolor;
        }
        padding : 1em;
        
      }
   
    ');
  
   //back button to navigation
   $back = a ( array ( "href" => "#navigation" ), "back" );  
  
  
   //Menu at the top
   $html .=  nav ( array ("id" => "navigation"),
                  h1 ( "Menu" ),
                  a ( array ("href" => "#introduction" ), "Introduction"), 
                  a ( array ("href" => "#overview"), "THTMLElement" ),
                  a ( array ("href" => "#examples"), "Examples"),
                  a ( array ("href" => "#h1" ), "h1"),
                  a ( array ("href" => "#h2" ), "h2"),
                  a ( array ("href" => "#h3" ), "h3")
              );
    
   $html .= article ( array("id" => "introduction"), 
              h2 ( "Introduction"),
              
              p ( "The shape language was designed for programmers who use normal coding to enable the output of wellformed HTML whilst making the HTML reusable and dynamic, you will see by looking at this code that we have used
                   variables to make the implementation of our examples without having to repeat the code." ), 
                   
              p ( "It is important to note that shape is able to compile SASS and coffeescript languages using the tags sass and coffeescript.  It means that you don't need write SASS and Coffeescript then compile it, the results are immediately available. " ),     
   
              p ( "Complex groups of tags can be stored in a variable and reused later, have a look at the essence of shape in the next section" ),
              
              $back
            ); 
    
   $html .= article ( array ("id" => "overview"),
         h2 ("THTMLElement"),
         
         p ("Here is an overview of the THTMLElement class on which all tags are based"),
         
         h4 ( "Properties" ),
         
         h5 (),
         
         h4 ( "Methods" ),
         
         h5 ("__contruct ( \$params ) "),
         
         h5 ("__contruct ( \$params ) "),
   
    $back
   ); 
   
   
   $html .= h1 (array ("id" => "examples"), "Examples");

   //example of an html example layout in shape - the variable $article and other variables can be reused over and over again - how cool is that ?!
   $article = article ( array("id" => "h1"), $tagname = h1 ( "H1"  ),
                      $tagdesc = p ( "Example of the H1 tag" ),
                      $expanded = shape (
                        section ( array ( "class" => "codeexample" ), h2 ( "Example:" ),
                          p (
                           $codeexample = code ( ' $html = h1(array ("style" => "color: blue"), "Example of an H1 tag"); ' )
                          )
                        )
                        ,
                        section ( array ( "class" => "codeoutput" ),  h2 ( "Output:" ), 
                           $codeoutput = p ( 
                             h1 ( array ("style" => "color: blue"), "Example of an H1 Tag") 
                           )  
                        )                  
                      ),   
                       
                      $back
                     );
   
   //Example for the H1 tag
   $html .= $article;
   
   //Example for the H2 tag
   $article->set_attribute ("id", "h2");
   $tagname->set_content ("H2");
   $tagdesc->set_content ("Example of an H2 tag");
   $codeexample->set_content (' $html = h2(array ("style" => "color: blue"), "Example of an H2 tag"); ');
   $codeoutput->set_content ( h2(array ("style" => "color: blue"), "Example of an H2 tag") );
   $html .= $article;

   //Example for the H3 tag
   $article->set_attribute ("id", "h3");
   $tagname->set_content ("H3");
   $tagdesc->set_content ("Example of an H3 tag");
   $codeexample->set_content (' $html = h3(array ("style" => "color: blue"), "Example of an H3 tag"); ');
   $codeoutput->set_content ( h3(array ("style" => "color: blue"), "Example of an H3 tag") );
   $html .= $article;


   //And finally lets output everything
   echo html ( head ( 
               title ("Help File for Shape") ), 
               body ( $html ) 
             );
?>