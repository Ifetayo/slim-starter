<?php

/* home.twig */
class __TwigTemplate_aae0720e76fccff7bc889ad41c5ff31590b885f38ea4b1779b51984da3c66552 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("default.twig", "home.twig", 1);
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
        echo " Home ";
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "<div class=\"starter-template\">
    <h1>Slim Starter Template</h1>
    <p class=\"lead\">Use this document as a way to quickly start any new project.
    \t<br> See the docs to get started.
    </p>
</div>
";
    }

    public function getTemplateName()
    {
        return "home.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  38 => 6,  35 => 5,  29 => 3,  11 => 1,);
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

{% block title %} Home {% endblock %}

{% block content %}
<div class=\"starter-template\">
    <h1>Slim Starter Template</h1>
    <p class=\"lead\">Use this document as a way to quickly start any new project.
    \t<br> See the docs to get started.
    </p>
</div>
{% endblock %}", "home.twig", "C:\\wamp64\\www\\slim-starter\\resources\\views\\home.twig");
    }
}
