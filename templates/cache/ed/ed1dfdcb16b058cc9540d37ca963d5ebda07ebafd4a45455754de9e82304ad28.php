<?php

/* layout.html.twig */
class __TwigTemplate_b108a1cdaf758116a6b9c7c899ea7f2acea202702a6c519da3bb600a1a4081b6 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<html>
    <head>
        <meta charset=\"utf-8\"/>
        ";
        // line 4
        $this->displayBlock('head', $context, $blocks);
        // line 28
        echo "    </head>
    <body>
        <h1>Slim</h1>
        <div>a microframework for PHP</div>
        ";
        // line 32
        $this->displayBlock('content', $context, $blocks);
        // line 33
        echo "    </body>
</html>
";
    }

    // line 4
    public function block_head($context, array $blocks = array())
    {
        // line 5
        echo "        <title><title>";
        $this->displayBlock('title', $context, $blocks);
        echo " Social Management</title></title>
        <link href='//fonts.googleapis.com/css?family=Lato:300' rel='stylesheet' type='text/css'>
        <style>
            body {
                margin: 50px 0 0 0;
                padding: 0;
                width: 100%;
                font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif;
                text-align: center;
                color: #aaa;
                font-size: 18px;
            }

            h1 {
                color: #719e40;
                letter-spacing: -3px;
                font-family: 'Lato', sans-serif;
                font-size: 100px;
                font-weight: 200;
                margin-bottom: 0;
            }
        </style>
        ";
    }

    public function block_title($context, array $blocks = array())
    {
    }

    // line 32
    public function block_content($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "layout.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  78 => 32,  46 => 5,  43 => 4,  37 => 33,  35 => 32,  29 => 28,  27 => 4,  22 => 1,);
    }
}
/* <html>*/
/*     <head>*/
/*         <meta charset="utf-8"/>*/
/*         {% block head %}*/
/*         <title><title>{% block title %}{% endblock %} Social Management</title></title>*/
/*         <link href='//fonts.googleapis.com/css?family=Lato:300' rel='stylesheet' type='text/css'>*/
/*         <style>*/
/*             body {*/
/*                 margin: 50px 0 0 0;*/
/*                 padding: 0;*/
/*                 width: 100%;*/
/*                 font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;*/
/*                 text-align: center;*/
/*                 color: #aaa;*/
/*                 font-size: 18px;*/
/*             }*/
/* */
/*             h1 {*/
/*                 color: #719e40;*/
/*                 letter-spacing: -3px;*/
/*                 font-family: 'Lato', sans-serif;*/
/*                 font-size: 100px;*/
/*                 font-weight: 200;*/
/*                 margin-bottom: 0;*/
/*             }*/
/*         </style>*/
/*         {% endblock %}*/
/*     </head>*/
/*     <body>*/
/*         <h1>Slim</h1>*/
/*         <div>a microframework for PHP</div>*/
/*         {% block content %}{% endblock %}*/
/*     </body>*/
/* </html>*/
/* */
