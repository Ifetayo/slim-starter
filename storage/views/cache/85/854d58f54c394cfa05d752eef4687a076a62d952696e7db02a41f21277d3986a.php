<?php

/* emails\auth\verifyemail.twig */
class __TwigTemplate_a0419749046d0bf8e190ca5d746ac701672fbb56df25f7494a9dbaba059a1d20 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<html>
<head>
\t<title>Welcome to Slim Starter</title>
</head>
<body>
Dear ";
        // line 6
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "first_name", array()), "html", null, true);
        echo ", welcome to slim starter. Please click on the link 
";
        // line 7
        echo twig_escape_filter($this->env, (isset($context["app_url"]) ? $context["app_url"] : null), "html", null, true);
        echo twig_escape_filter($this->env, $this->env->getExtension('Slim\Views\TwigExtension')->pathFor("auth.verify", array(), array("email" => twig_urlencode_filter($this->getAttribute((isset($context["user"]) ? $context["user"] : null), "email", array())), "token" => twig_urlencode_filter((isset($context["token"]) ? $context["token"] : null)))), "html", null, true);
        echo " inorder to verify your email address.
Thank you.
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "emails\\auth\\verifyemail.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  30 => 7,  26 => 6,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("<html>
<head>
\t<title>Welcome to Slim Starter</title>
</head>
<body>
Dear {{ user.first_name }}, welcome to slim starter. Please click on the link 
{{ app_url }}{{ path_for('auth.verify', [], {'email' : user.email|url_encode, 'token' : token|url_encode}) }} inorder to verify your email address.
Thank you.
</body>
</html>", "emails\\auth\\verifyemail.twig", "C:\\wamp64\\www\\slim-starter\\resources\\views\\emails\\auth\\verifyemail.twig");
    }
}
