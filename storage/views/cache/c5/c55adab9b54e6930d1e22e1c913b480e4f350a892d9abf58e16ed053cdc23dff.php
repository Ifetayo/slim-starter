<?php

/* auth\signup.twig */
class __TwigTemplate_40755c0fb9f411500f75cab25a34e3032ae51e62edd05c663f44626cc93a4ac1 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("default.twig", "auth\\signup.twig", 1);
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
        echo " Sign Up ";
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "\t<div class=\"row\">
\t<div class=\"col-md-6 col-md-offset-3\">
\t\t<div class=\"panel panel-default\">
\t\t\t<div class=\"panel-heading\">Sign Up</div>

\t\t\t<div class=\"panel-body\">\t
\t\t\t
\t\t\t\t<form action=\"";
        // line 13
        echo twig_escape_filter($this->env, $this->env->getExtension('Slim\Views\TwigExtension')->pathFor("auth.signup"), "html", null, true);
        echo "\" method=\"post\">
\t\t\t\t
\t\t\t\t<div class=\"form-group ";
        // line 15
        echo (($this->getAttribute((isset($context["errors"]) ? $context["errors"] : null), "email", array())) ? (" has-error") : (""));
        echo "\">
\t\t\t\t\t<label for=\"email\">Email</label>
\t\t\t\t\t<input type=\"email\" name=\"email\" id=\"email\" placeholder=\"you@domain.com\" class=\"form-control\" value=\"";
        // line 17
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["old"]) ? $context["old"] : null), "email", array()), "html", null, true);
        echo "\" />
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
        echo (($this->getAttribute((isset($context["errors"]) ? $context["errors"] : null), "name", array())) ? (" has-error") : (""));
        echo "\">
\t\t\t\t\t<label for=\"name\">Name</label>
\t\t\t\t\t<input type=\"text\" name=\"name\" id=\"name\" class=\"form-control\" value=\"";
        // line 25
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["old"]) ? $context["old"] : null), "name", array()), "html", null, true);
        echo "\"/>
\t\t\t\t\t";
        // line 26
        if ($this->getAttribute((isset($context["errors"]) ? $context["errors"] : null), "name", array())) {
            // line 27
            echo "\t\t\t\t\t\t<span class=\"help-block\">";
            echo twig_escape_filter($this->env, twig_first($this->env, $this->getAttribute((isset($context["errors"]) ? $context["errors"] : null), "name", array())), "html", null, true);
            echo "</span>
\t\t\t\t\t";
        }
        // line 29
        echo "\t\t\t\t</div>

\t\t\t\t<div class=\"form-group ";
        // line 31
        echo (($this->getAttribute((isset($context["errors"]) ? $context["errors"] : null), "password", array())) ? (" has-error") : (""));
        echo "\">
\t\t\t\t\t<label for=\"password\">Password</label>
\t\t\t\t\t<input type=\"password\" name=\"password\" id=\"password\" class=\"form-control\"/>
\t\t\t\t\t";
        // line 34
        if ($this->getAttribute((isset($context["errors"]) ? $context["errors"] : null), "password", array())) {
            // line 35
            echo "\t\t\t\t\t\t<span class=\"help-block\">";
            echo twig_escape_filter($this->env, twig_first($this->env, $this->getAttribute((isset($context["errors"]) ? $context["errors"] : null), "password", array())), "html", null, true);
            echo "</span>
\t\t\t\t\t";
        }
        // line 37
        echo "\t\t\t\t</div>

\t\t\t\t<button type=\"submit\" class=\"btn btn-default\">Sign Up</button>
\t\t\t\t";
        // line 40
        echo $this->getAttribute((isset($context["csrf"]) ? $context["csrf"] : null), "field", array());
        echo "
\t\t\t\t</form>
\t\t\t</div>
\t\t</div>
\t</div>
</div>
";
    }

    public function getTemplateName()
    {
        return "auth\\signup.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  113 => 40,  108 => 37,  102 => 35,  100 => 34,  94 => 31,  90 => 29,  84 => 27,  82 => 26,  78 => 25,  73 => 23,  69 => 21,  63 => 19,  61 => 18,  57 => 17,  52 => 15,  47 => 13,  38 => 6,  35 => 5,  29 => 3,  11 => 1,);
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

{% block title %} Sign Up {% endblock %}

{% block content %}
\t<div class=\"row\">
\t<div class=\"col-md-6 col-md-offset-3\">
\t\t<div class=\"panel panel-default\">
\t\t\t<div class=\"panel-heading\">Sign Up</div>

\t\t\t<div class=\"panel-body\">\t
\t\t\t
\t\t\t\t<form action=\"{{ path_for('auth.signup') }}\" method=\"post\">
\t\t\t\t
\t\t\t\t<div class=\"form-group {{ errors.email ? ' has-error' : '' }}\">
\t\t\t\t\t<label for=\"email\">Email</label>
\t\t\t\t\t<input type=\"email\" name=\"email\" id=\"email\" placeholder=\"you@domain.com\" class=\"form-control\" value=\"{{ old.email }}\" />
\t\t\t\t\t{% if errors.email%}
\t\t\t\t\t\t<span class=\"help-block\">{{ errors.email | first }}</span>
\t\t\t\t\t{% endif %}
\t\t\t\t</div>

\t\t\t\t<div class=\"form-group {{ errors.name ? ' has-error' : '' }}\">
\t\t\t\t\t<label for=\"name\">Name</label>
\t\t\t\t\t<input type=\"text\" name=\"name\" id=\"name\" class=\"form-control\" value=\"{{ old.name }}\"/>
\t\t\t\t\t{% if errors.name%}
\t\t\t\t\t\t<span class=\"help-block\">{{ errors.name | first }}</span>
\t\t\t\t\t{% endif %}
\t\t\t\t</div>

\t\t\t\t<div class=\"form-group {{ errors.password ? ' has-error' : '' }}\">
\t\t\t\t\t<label for=\"password\">Password</label>
\t\t\t\t\t<input type=\"password\" name=\"password\" id=\"password\" class=\"form-control\"/>
\t\t\t\t\t{% if errors.password %}
\t\t\t\t\t\t<span class=\"help-block\">{{ errors.password | first }}</span>
\t\t\t\t\t{% endif %}
\t\t\t\t</div>

\t\t\t\t<button type=\"submit\" class=\"btn btn-default\">Sign Up</button>
\t\t\t\t{{ csrf.field | raw }}
\t\t\t\t</form>
\t\t\t</div>
\t\t</div>
\t</div>
</div>
{% endblock %}", "auth\\signup.twig", "C:\\wamp64\\www\\slim-starter\\resources\\views\\auth\\signup.twig");
    }
}
