<?php

/* partials/alert.twig */
class __TwigTemplate_90fa218be19cfaf9cf02a482aea24ff6ee56926ba05543445c5fbe10a26104fc extends Twig_Template
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
        if ($this->getAttribute((isset($context["flash"]) ? $context["flash"] : null), "getMessage", array(0 => "info"), "method")) {
            // line 2
            echo "<div class=\"alert alert-info alert-dismissable\" style=\"margin-top: 25px\" align=\"center\">
\t <button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
\t";
            // line 4
            echo twig_escape_filter($this->env, twig_first($this->env, $this->getAttribute((isset($context["flash"]) ? $context["flash"] : null), "getMessage", array(0 => "info"), "method")), "html", null, true);
            echo "
</div>
";
        } elseif ($this->getAttribute(        // line 6
(isset($context["flash"]) ? $context["flash"] : null), "getMessage", array(0 => "success"), "method")) {
            // line 7
            echo "<div class=\"alert alert-success alert-dismissable\" style=\"margin-top: 25px\" align=\"center\">
\t <button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
\t";
            // line 9
            echo twig_escape_filter($this->env, twig_first($this->env, $this->getAttribute((isset($context["flash"]) ? $context["flash"] : null), "getMessage", array(0 => "success"), "method")), "html", null, true);
            echo "
</div>
";
        }
    }

    public function getTemplateName()
    {
        return "partials/alert.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  36 => 9,  32 => 7,  30 => 6,  25 => 4,  21 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% if flash.getMessage('info') %}
<div class=\"alert alert-info alert-dismissable\" style=\"margin-top: 25px\" align=\"center\">
\t <button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
\t{{ flash.getMessage('info') | first }}
</div>
{% elseif flash.getMessage('success') %}
<div class=\"alert alert-success alert-dismissable\" style=\"margin-top: 25px\" align=\"center\">
\t <button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
\t{{ flash.getMessage('success') | first }}
</div>
{% endif %}", "partials/alert.twig", "C:\\wamp64\\www\\slim-starter\\resources\\views\\partials\\alert.twig");
    }
}
