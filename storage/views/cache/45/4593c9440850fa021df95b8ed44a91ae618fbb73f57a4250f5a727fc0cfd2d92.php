<?php

/* auth\signin.twig */
class __TwigTemplate_309c9455acf6d0809f8e24eef7415671f44325e73b390bb0c8f02b41df9a2bb8 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("default.twig", "auth\\signin.twig", 1);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "default.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo " Signin ";
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "\t<div class=\"row\">
\t<div class=\"col-md-6 col-md-offset-3\">
\t\t<div class=\"panel panel-default\">
\t\t\t<div class=\"panel-heading\">Signin</div>

\t\t\t<div class=\"panel-body\">\t\t\t\t

\t\t\t\t<form autocomplete=\"off\" action=\"#\" method=\"post\">
\t\t\t\t
\t\t\t\t<div class=\"form-group ";
        // line 15
        echo (($this->getAttribute((isset($context["errors"]) ? $context["errors"] : null), "email", array())) ? (" has-error") : (""));
        echo "\">
\t\t\t\t\t<label for=\"email\">Email</label>
\t\t\t\t\t<input type=\"email\" name=\"email\" id=\"email\" placeholder=\"you@domain.com\" class=\"form-control\" value=\"";
        // line 17
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["old"]) ? $context["old"] : null), "email", array()), "html", null, true);
        echo "\"/>
\t\t\t\t\t";
        // line 18
        if ($this->getAttribute((isset($context["errors"]) ? $context["errors"] : null), "email", array())) {
            // line 19
            echo "\t\t\t\t\t\t<span class=\"help-block\">";
            echo twig_escape_filter($this->env, twig_first($this->env, $this->getAttribute((isset($context["errors"]) ? $context["errors"] : null), "email", array())), "html", null, true);
            echo "</span>
\t\t\t\t\t";
        }
        // line 21
        echo "\t\t\t\t</div>

\t\t\t\t<div class=\"form-group ";
        // line 23
        echo (($this->getAttribute((isset($context["errors"]) ? $context["errors"] : null), "password", array())) ? (" has-error") : (""));
        echo "\">
\t\t\t\t\t<label for=\"password\">Password</label>
\t\t\t\t\t<input type=\"password\" name=\"password\" id=\"password\" class=\"form-control\"/>
\t\t\t\t\t";
        // line 26
        if ($this->getAttribute((isset($context["errors"]) ? $context["errors"] : null), "password", array())) {
            // line 27
            echo "\t\t\t\t\t\t<span class=\"help-block\">";
            echo twig_escape_filter($this->env, twig_first($this->env, $this->getAttribute((isset($context["errors"]) ? $context["errors"] : null), "password", array())), "html", null, true);
            echo "</span>
\t\t\t\t\t";
        }
        // line 29
        echo "\t\t\t\t</div>

\t\t\t\t<button type=\"submit\" class=\"btn btn-default\">Signin</button>
\t\t\t\t
\t\t\t\t</form>
\t\t\t</div>
\t\t</div>
\t</div>
</div>
";
    }

    public function getTemplateName()
    {
        return "auth\\signin.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  84 => 29,  78 => 27,  76 => 26,  70 => 23,  66 => 21,  60 => 19,  58 => 18,  54 => 17,  49 => 15,  38 => 6,  35 => 5,  29 => 3,  11 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends 'default.twig' %}

{% block title %} Signin {% endblock %}

{% block content %}
\t<div class=\"row\">
\t<div class=\"col-md-6 col-md-offset-3\">
\t\t<div class=\"panel panel-default\">
\t\t\t<div class=\"panel-heading\">Signin</div>

\t\t\t<div class=\"panel-body\">\t\t\t\t

\t\t\t\t<form autocomplete=\"off\" action=\"#\" method=\"post\">
\t\t\t\t
\t\t\t\t<div class=\"form-group {{ errors.email ? ' has-error' : '' }}\">
\t\t\t\t\t<label for=\"email\">Email</label>
\t\t\t\t\t<input type=\"email\" name=\"email\" id=\"email\" placeholder=\"you@domain.com\" class=\"form-control\" value=\"{{ old.email }}\"/>
\t\t\t\t\t{% if errors.email%}
\t\t\t\t\t\t<span class=\"help-block\">{{ errors.email | first }}</span>
\t\t\t\t\t{% endif %}
\t\t\t\t</div>

\t\t\t\t<div class=\"form-group {{ errors.password ? ' has-error' : '' }}\">
\t\t\t\t\t<label for=\"password\">Password</label>
\t\t\t\t\t<input type=\"password\" name=\"password\" id=\"password\" class=\"form-control\"/>
\t\t\t\t\t{% if errors.password %}
\t\t\t\t\t\t<span class=\"help-block\">{{ errors.password | first }}</span>
\t\t\t\t\t{% endif %}
\t\t\t\t</div>

\t\t\t\t<button type=\"submit\" class=\"btn btn-default\">Signin</button>
\t\t\t\t
\t\t\t\t</form>
\t\t\t</div>
\t\t</div>
\t</div>
</div>
{% endblock %}", "auth\\signin.twig", "C:\\wamp64\\www\\slim-starter\\resources\\views\\auth\\signin.twig");
    }
}
