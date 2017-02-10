<?php

/* partials/navigation.twig */
class __TwigTemplate_50769f726004a75058b3be50edb5f1623bc263aeb175b9a1fb24cebe1666431b extends Twig_Template
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
        echo "<nav class=\"navbar navbar-inverse navbar-fixed-top\">
  <div class=\"container\">
    <div class=\"navbar-header\">
      <button type=\"button\" class=\"navbar-toggle collapsed\"
      data-toggle=\"collapse\" data-target=\"#navbar\"
      aria-expanded=\"false\" aria-controls=\"navbar\">

      <span class=\"sr-only\"> Toggle navigation
      </span>
      <span class=\"icon-bar\"></span>
      <span class=\"icon-bar\"></span>
      <span class=\"icon-bar\"></span>
      </button>
      <a class=\"navbar-brand\" href=\"\"> Slim Starter</a>
    </div>
    <div id=\"navbar\" class=\"collapse navbar-collapse\">      
      <ul class=\"nav navbar-nav navbar-right\">        
        <li class=\"dropdown\">
          <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">
            <span class=\"caret\">
            </span>
          </a>
          <ul class=\"dropdown-menu\">
            <li><a href=\"#\">Change password</a>
            </li>
            <li><a href=\"# \">Sign out</a></li>
          </ul>
        </li>        
        <li><a href=\"";
        // line 29
        echo twig_escape_filter($this->env, $this->env->getExtension('Slim\Views\TwigExtension')->pathFor("auth.signup"), "html", null, true);
        echo "\">Sign up</a></li>
        <li><a href=\"";
        // line 30
        echo twig_escape_filter($this->env, $this->env->getExtension('Slim\Views\TwigExtension')->pathFor("auth.signin"), "html", null, true);
        echo "\">Sign in</a></li>
      </ul>
    </div>
  </div>
</nav>";
    }

    public function getTemplateName()
    {
        return "partials/navigation.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  53 => 30,  49 => 29,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("<nav class=\"navbar navbar-inverse navbar-fixed-top\">
  <div class=\"container\">
    <div class=\"navbar-header\">
      <button type=\"button\" class=\"navbar-toggle collapsed\"
      data-toggle=\"collapse\" data-target=\"#navbar\"
      aria-expanded=\"false\" aria-controls=\"navbar\">

      <span class=\"sr-only\"> Toggle navigation
      </span>
      <span class=\"icon-bar\"></span>
      <span class=\"icon-bar\"></span>
      <span class=\"icon-bar\"></span>
      </button>
      <a class=\"navbar-brand\" href=\"\"> Slim Starter</a>
    </div>
    <div id=\"navbar\" class=\"collapse navbar-collapse\">      
      <ul class=\"nav navbar-nav navbar-right\">        
        <li class=\"dropdown\">
          <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">
            <span class=\"caret\">
            </span>
          </a>
          <ul class=\"dropdown-menu\">
            <li><a href=\"#\">Change password</a>
            </li>
            <li><a href=\"# \">Sign out</a></li>
          </ul>
        </li>        
        <li><a href=\"{{ path_for('auth.signup')}}\">Sign up</a></li>
        <li><a href=\"{{ path_for('auth.signin')}}\">Sign in</a></li>
      </ul>
    </div>
  </div>
</nav>", "partials/navigation.twig", "C:\\wamp64\\www\\slim-starter\\resources\\views\\partials\\navigation.twig");
    }
}
