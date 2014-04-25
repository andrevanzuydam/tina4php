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
        width: 100%;
        a {
          display: inline-block;
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
          background: $codebgcolor;
          padding: 0.5em;
        }
        
        padding : 1em;
      }
      
      .codeoutput {
        h2 {
          text-decoration: underline;
        }
        p {
          background: $codebgcolor;
        }
        padding : 1em;
        
      }
   
    ');
  
   //back button to navigation
   $back = a ( array ( "href" => "#navigation" ), "back" );  
  
  
   //Menu at the top
   $html .=  nav ( array ("id" => "navigation"),
                  h1("Menu"), 
                  a ( array ("href" => "#introduction" ), "Introduction"), 
                  a ( array ("href" => "#overview"), "THTMLElement" ),
                  a ( array ("href" => "#examples"), "Examples"),
                  a ( array ("href" => "#h1" ), "h1"),
                  a ( array ("href" => "#h2" ), "h2"),
                  a ( array ("href" => "#h3" ), "h3"),
                  a ( array ("href" => "#h4" ), "h4"),
                  a ( array ("href" => "#h5" ), "h5"),
                  a ( array ("href" => "#h6" ), "h6"),
                  a ( array ("href" => "#p" ), "p"),
                  a ( array ("href" => "#b" ), "b"),
                  a ( array ("href" => "#strong" ), "strong" ),
                  a ( array ("href" => "#i"), "i" ),
                  a ( array ("href" => "#em"), "em" ),
                  a ( array ("href" => "#u"), "u" ),
                  a ( array ("href" => "#center" ), "center"),
                  a ( array ("href" => "#del"), "del"),
                  a ( array ("href" => "#abbr"),"abbr" ),
                  a ( array ("href" => "#sub"), "sub" ),
                  a ( array ("href" => "#sup"), "sup" ),
                  a ( array ("href" => "#mark"), "mark" ),
                  a ( array ("href" => "#ul"), "ul" ), 
                  a ( array ("href" => "#ol"), "ol" ),
                  a ( array ("href" => "#dl"), "dl" ),
                  a ( array ("href" => "#bdo"), "bdo" ),
                  a ( array ("href" => "#a"), "a" ),
                  a ( array ("href" => "#table"), "table" ),
                  a ( array ("href" => "#article"), "article" ),
                  a ( array ("href" => "#img"), "img" ),
                  a ( array ("href" => "#select"), "select" ),
                  a ( array ("href" => "#button"), "button" ),
                  a ( array ("href" => "#br"), "br" ),
                  a ( array ("href" => "#hr"), "hr" ),
                  a ( array ("href" => "#figure"), "figure" )
                                               
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
                      $expanded = shape ( section ( array ( "class" => "codeexample" ), h2 ( "Example:" ),
                          p ( $codeexample = code ( ' $html .= h1(array ("style" => "color: blue"), "Example of an H1 tag"); ' ) ) ),
                        section ( array ( "class" => "codeoutput" ),  h2 ( "Output:" ), 
                           $codeoutput = p ( h1 ( array ("style" => "color: blue"), "Example of an H1 Tag") ) ) ),
                      $back
                     );
   
   //Example for the H1 tag
   $html .= $article;
   
   //Example for the H2 tag
   $article->set_attribute ("id", "h2");
   $tagname->set_content ("H2");
   $tagdesc->set_content ("Example of an h2 tag");
   $codeexample->set_content (' $html .= h2(array ("style" => "color: magenta"), "Example of an H2 tag"); ');
   $codeoutput->set_content ( h2(array ("style" => "color: magenta"), "Example of an H2 tag") );
   $html .= $article;

   //Example for the H3 tag
   $article->set_attribute ("id", "h3");
   $tagname->set_content ("H3");
   $tagdesc->set_content ("Example of an h3 tag");
   $codeexample->set_content (' $html .= h3(array ("style" => "color: cyan"), "Example of an H3 tag"); ');
   $codeoutput->set_content ( h3(array ("style" => "color: cyan"), "Example of an H3 tag") );
   $html .= $article;
   
   //Example for the H4 tag
   $article->set_attribute ("id", "h4");
   $tagname->set_content ("H4");
   $tagdesc->set_content ("Example of an h4 tag");
   $codeexample->set_content (' $html .= h4(array ("style" => "color: orange"), "Example of an H4 tag"); ');
   $codeoutput->set_content ( h4(array ("style" => "color: orange"), "Example of an H4 tag") );
   $html .= $article;
  
   //Example for the H5 tag
   $article->set_attribute ("id", "h5");
   $tagname->set_content ("H5");
   $tagdesc->set_content ("Example of an h5 tag");
   $codeexample->set_content (' $html .= h5("Example of an H5 tag"); '  );
   $codeoutput->set_content ( h5("Example of an H5 tag") );
   $html .= $article;
   
   //Example for the H6 tag
   $article->set_attribute ("id", "h6");
   $tagname->set_content ("H6");
   $tagdesc->set_content ("Example of an h6 tag");
   $codeexample->set_content (' $html .= h6(array ("style" => "color: green"), "Example of an H6 tag"); ');
   $codeoutput->set_content ( h3(array ("style" => "color: green"), "Example of an H6 tag") );
   $html .= $article;
   
   //Example for the p tag
   $article->set_attribute ("id", "p");
   $tagname->set_content ("P");
   $tagdesc->set_content ("Example of a P tag, used to put content in a paragraph");
   $codeexample->set_content (' $html .= p(array ("style" => "color: blue"), This is some text in a paragraph."); ');
   $codeoutput->set_content ( p(array ("style" => "color: blue"), "This is some text in a paragraph.") );
   $html .= $article;
   
   //Example for the b tag
   $article->set_attribute ("id", "b");
   $tagname->set_content ("B");
   $tagdesc->set_content ("Example of a b tag, used to make text bold");
   $codeexample->set_content (' $html .= p(array ("style" => "color: blue"), "This word is ", b("bold.")); ');
   $codeoutput->set_content ( p(array ("style" => "color: blue"), "This word is ", b("bold.")) );
   $html .= $article;
   
   //Example for the STRONG tag
   $article->set_attribute ("id", "strong");
   $tagname->set_content ("STRONG");
   $tagdesc->set_content ("Example of the strong tag, used to make text bold as well");
   $codeexample->set_content (' $html .= strong("This is text is also bold, but the strong tag is used."); ');
   $codeoutput->set_content ( strong("This is text is also bold, but the strong tag is used.") );
   $html .= $article;
   
   //Example of the I tag
   $article->set_attribute ("id", "i");
   $tagname->set_content ("I");
   $tagdesc->set_content ("Example of the i tag, used to make text italics");
   $codeexample->set_content (' $html .= p("This is in ", i(array("style" => "color:red"), "italics.")); ');
   $codeoutput->set_content ( p("This is in ", i(array("style" => "color:red"), "italics") ));
   $html .= $article;
   
   //Example of the EM tag
   $article->set_attribute ("id", "em");
   $tagname->set_content ("EM");
   $tagdesc->set_content ("Example of the em tag, used to emphasize text by also putting it in italics");
   $codeexample->set_content (' $html .= em(array("style" => "color:black"), This sentence is emphasized.);');
   $codeoutput->set_content ( em(array("style" => "color:black"), "This sentence is emphasized."));
   $html .= $article;
   
   //Example of the U tag
   $article->set_attribute ("id", "u");
   $tagname->set_content ("U");
   $tagdesc->set_content ("Example of the u tag, used to underline specified text.");
   $codeexample->set_content (' $html .= u(array("style"=>"color:gray"), "underlined text here."); ');
   $codeoutput->set_content ( u(array("style"=>"color:gray"), "underlined text here.") );
   $html .= $article;

   //Example of the CENTER tag
   $article->set_attribute ("id", "center");
   $tagname->set_content ("CENTER");
   $tagdesc->set_content ("Example of the center tag, used to center allign specified text.");
   $codeexample->set_content (' $html .= center(array("style"=>"color:magenta"), "This text is centered."); ');
   $codeoutput->set_content ( center(array("style"=>"color:magenta"), "This text is centered.") );
   $html .= $article;
   
   //Example of the DEL tag
   $article->set_attribute ("id", "del");
   $tagname->set_content ("DEL");
   $tagdesc->set_content ("Example of the del tag, used to cross out incorrect text, strike tag may also be used.");
   $codeexample->set_content (' $html .= p(array("style" =>"color:black"), "this text is ", del("crossed out. "), strike("this is striked out.")); ');
   $codeoutput->set_content (p(array("style" =>"color:black"), "this text is ", del("crossed out. "), strike("this is striked out.")));
   $html .= $article;
   
   //Example of the ABBR tag
   $article->set_attribute ("id", "abbr");
   $tagname->set_content ("ABBR");
   $tagdesc->set_content ("Example of the abbr tag, used to indicate abbreviations or acronyms.");
   $codeexample->set_content (' $html .= p(array("style" => "color:red"), "This is an acronym, hold your cursor over it to see its title: ", abbr(array("title" => "World Wide Web"), "www" )); ');
   $codeoutput->set_content ( p(array("style" => "color:red"), "This is an acronym, hold your cursor over it to see its title: ", abbr(array("title" => "World Wide Web"), "www" )) ); 
   $html .= $article;
   
   //Example of the SUB tag
   $article->set_attribute ("id", "sub");
   $tagname->set_content ("SUB");
   $tagdesc->set_content ("Example of the sub tag, used to put text as subscript.");
   $codeexample->set_content (' $html .= p("a water molecules formula is: H". sub("2"). "O"); ');
   $codeoutput->set_content ( p("a water molecules formula is: H". sub("2"). "O") );
   $html .= $article;
   
   //Example of the SUP tag
   $article->set_attribute ("id", "sup");
   $tagname->set_content ("SUP");
   $tagdesc->set_content ("Example of the sup tag, used to put text as superscript.");
   $codeexample->set_content (' $html .= p(array("style" => "color:blue")"a number like 4 squared can be written as: 4", sup("2")); ');
   $codeoutput->set_content ( p(array("style" => "color:blue"), "a number like 4 squared can be written as: 4", sup("2")) );
   $html .= $article;
   
   //Example of the MARK tag
   $article->set_attribute ("id", "mark");
   $tagname->set_content ("MARK");
   $tagdesc->set_content ("Example of the mark tag, used to highlight text");
   $codeexample->set_content (' $html .= p(array("style" => "color:purple"), "this word is ", mark("highlighted.")); ');
   $codeoutput->set_content ( p(array("style" => "color:purple"), "this word is ", mark("highlighted.")) );
   $html .= $article;
   
   //Example of the UL tag
   $article->set_attribute ("id", "ul");
   $tagname->set_content ("UL");
   $tagdesc->set_content ("Example of the ul tag, used to create unordered lists, use li (list item) tag to add items to the list.");
   $codeexample->set_content (' $html .= ul(li("coffee"), li("tea"), li("Fanta")); ');
   $codeoutput->set_content (ul(li("coffee"), li("tea"), li("Fanta")));
   $html .= $article;
   
   //Example of the OL tag
   $article->set_attribute ("id", "ol");
   $tagname->set_content ("OL");
   $tagdesc->set_content ("Example of the ol tag, used to create ordered lists, also uses li tag to add items to it.");
   $codeexample->set_content (' $html .= ol(li("coffee"), li("tea"), li("Fanta")); ');
   $codeoutput->set_content ( ol(li("coffee"), li("tea"), li("Fanta")) );
   $html .= $article;
   
   //Example of the DL tag
   $article->set_attribute ("id", "dl");
   $tagname->set_content ("DL");
   $tagdesc->set_content ("Example of the dl tag, used to create descriptive lists, uses dt and dd tags to add items and descriptions to the list.");
   $codeexample->set_content (' $html .= dl( dt("coffee"), dd("black, hot drink"), dt("tea"), dd("red, hot drink"), dt("Fanta"), dd("sweet, fizzy drink") ); ');                            
   $codeoutput->set_content ( dl( dt("coffee"), dd("black, hot drink"), dt("tea"), dd("red, hot drink"), dt("Fanta"), dd("sweet, fizzy drink") ) );
   $html .= $article;
   
   //Example of the BDO tag
   $article->set_attribute ("id", "bdo");
   $tagname->set_content ("BDO");
   $tagdesc->set_content ("Example of the bdo tag. BDO stands for bi-directional override, it is used to reverse direction of text: left to right(ltr) or right to left(rtl).");
   $codeexample->set_content (' $html .= bdo(array("dir" => "ltr"), "this text is left to right. ", bdo(array("dir" => "rtl"), "this text goes right to left.")); ');
   $codeoutput->set_content ( bdo(array("dir" => "ltr"), "this text is left to right. ", bdo(array("dir" => "rtl"), "this text goes right to left.")) );
   $html .= $article;
   
   // Example of the A tag
   $article->set_attribute ("id", "a");
   $tagname->set_content ("A");
   $tagdesc->set_content ("Example of the a tag, it is an anchor tag used for hyperlinks, etc.");
   $codeexample->set_content (' $html .= a(array("href" => "http://www.google.com", "target" => "_blank"), "click me to go to google."); ');
   $codeoutput->set_content ( a(array("href" => "http://www.google.com", "target" => "_blank"), "click me to go to google.") );
   $html .= $article;

   // Example of the TABLE tag
   $article->set_attribute ("id", "table");
   $tagname->set_content ("TABLE");
   $tagdesc->set_content ("Example of the table tag, used to create tables, use tr(table row), th(table header) and td(table cell) tags inside the table tag to create cells, rows and headers.");
   $codeexample->set_content (' $html .= table(array("border" => "1"), th( td("ITEM"), td("QUANTITY") ), tr( td("Books"), td("11") ), tr( td("Pens"), td("7") ) ); ');
   $codeoutput->set_content ( table(array("border" => "1"), th("ITEM"), th("QUANTITY"), tr( td("Books"), td("11") ), tr( td("Pens"), td("7") ) ) );
   $html .= $article;
   
   // Example of the ARTICLE tag
   $article->set_attribute ("id", "article");
   $tagname->set_content ("ARTICLE");
   $tagdesc->set_content ("Example of the article tag, used to contain specified independant content.");
   $codeexample->set_content (' $html .= article ( h1("Shape"), p("Shape is a language that simplifies HTML and adds new features like coffeescript, etc.") ); ');
   $codeoutput->set_content ( article ( h1("Shape"), p("Shape is a language that simplifies HTML and adds new features like coffeescript, etc.") ) );
   $html .= $article;
   
   // Example of the IMG tag
   $article->set_attribute ("id", "img");
   $tagname->set_content ("IMG");
   $tagdesc->set_content ("Example of the img tag, used to insert images.");
   $codeexample->set_content (' $html .= img(array("alt"=>"smiley face", "src"=>"http://upload.wikimedia.org/wikipedia/commons/thumb/8/85/Smiley.svg/800px-Smiley.svg.png", "width"=>"250px", "height"=>"250px")); ');
   $codeoutput->set_content ( img(array("alt"=>"smiley face", "src"=>"http://upload.wikimedia.org/wikipedia/commons/thumb/8/85/Smiley.svg/800px-Smiley.svg.png", "width"=>"250px", "height"=>"250px")) );
   $html .= $article;
   
    // Example of the SELECT tag
    $article->set_attribute ("id", "select");
    $tagname->set_content ("SELECT");
    $tagdesc->set_content ("Example of the select tag, used to create a drop-down list. Use optgroup and option tags to create list options to select from.");
    $codeexample->set_content (' $html .=  select(array("name"=>"Things"), optgroup(array("label"=>"Food"), option("Pizza"), option("Hamburgers")), optgroup(array("label"=>"Drinks"), option("Water"), option("Coke"))); ');
    $codeoutput->set_content ( select(array("name"=>"Things"), optgroup(array("label"=>"Food"), option("Pizza"), option("Hamburgers")), optgroup(array("label"=>"Drinks"), option("Water"), option("Coke"))) );
    $html .= $article;
   
    // Example of the BUTTON tag
    $article->set_attribute ("id", "button");
    $tagname->set_content ("BUTTON  ");
    $tagdesc->set_content ("Example of the select button, used to create a button that can perform various tasks.");
    $codeexample->set_content (' $html .= button(array("type"=>"button", "onclick"=>"alert("You clicked the button.")"), "Click this button." ); ');
    $codeoutput->set_content ( button(array("type"=>"button", "onclick"=>"alert('You clicked the button.')"), "Click this button." ) );
    $html .= $article;
    
   // Example of the BR tag
   $article->set_attribute ("id", "br");
   $tagname->set_content ("BR");
   $tagdesc->set_content ("Example of the br tag, used to create single line breaks.");
   $codeexample->set_content (' $html .=  p("this paragraph". br(). "contains a break."); ');
   $codeoutput->set_content ( p("this paragraph". br(). "contains a break.") );
   $html .= $article;
   
   // Example of the HR tag
   $article->set_attribute ("id", "hr");
   $tagname->set_content ("HR");
   $tagdesc->set_content ("Example of the hr tag, used to create horizontal lines that seperate sections.");
   $codeexample->set_content (' $html .=  p("this paragraph". hr(). "is seperated.". hr(). "by lines."); ');
   $codeoutput->set_content ( p("this paragraph". hr(). "is seperated.". hr(). "by lines.") );
   $html .= $article;
   
   // Example of the FIGURE tag
   $article->set_attribute ("id", "figure");
   $tagname->set_content ("FIGURE");
   $tagdesc->set_content ("Example of the figure tag, used to illustrate drawings, graphs, statistics, etc. Use figcaption tag to add a caption.");
   $codeexample->set_content (' $html .=  figure( img(array("alt"=>"smiley face", "src"=>"http://upload.wikimedia.org/wikipedia/commons/thumb/8/85/Smiley.svg/800px-Smiley.svg.png", "width"=>"250px", "height"=>"250px")),figcaption("figure 1: a smiley face.") ); ');
   $codeoutput->set_content (  figure( img(array("alt"=>"smiley face", "src"=>"http://upload.wikimedia.org/wikipedia/commons/thumb/8/85/Smiley.svg/800px-Smiley.svg.png", "width"=>"250px", "height"=>"250px")),figcaption("figure 1: a smiley face.") ) );
   $html .= $article;
   
   //And finally lets output everything
   echo html ( head ( 
               title ("Help File for Shape") ), 
               body ( $html ) 
             );
?>