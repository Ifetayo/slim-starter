<?php

/* partials/alert.twig */
class __TwigTemplate_3439dad7d2570098278d156cac2819a270616cf691c6a4e8cc36b0b2f997a10d extends Twig_Template
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
(isset($context["flash"]) ? $context["flash"] : null), "getMessage", array(0 => "reg-success"), "method")) {
            // line 7
            echo "<div class=\"alert alert-success alert-dismissable\" style=\"margin-top: 25px\" align=\"center\">
\t <button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
\t";
            // line 9
            echo twig_escape_filter($this->env, twig_first($this->env, $this->getAttribute((isset($context["flash"]) ? $context["flash"] : null), "getMessage", array(0 => "reg-success"), "method")), "html", null, true);
            echo "
\t<form class=\"form-horizontal\" method=\"POST\" action=\"";
            // line 10
            echo twig_escape_filter($this->env, $this->env->getExtension('Slim\Views\TwigExtension')->pathFor("auth.resend.token"), "html", null, true);
            echo "\" role=\"form\">
\t\t<input type=\"hidden\" name=\"token\" value=\"";
            // line 11
            echo twig_escape_filter($this->env, twig_first($this->env, $this->getAttribute((isset($context["flash"]) ? $context["flash"] : null), "getMessage", array(0 => "token"), "method")), "html", null, true);
            echo "\"/>
    \t<input type=\"hidden\" name=\"email\" value=\"";
            // line 12
            echo twig_escape_filter($this->env, twig_first($this->env, $this->getAttribute((isset($context["flash"]) ? $context["flash"] : null), "getMessage", array(0 => "email"), "method")), "html", null, true);
            echo "\"/>
    \t<button class=\"btn btn-danger btn-wide\" type=\"submit\">
              Resend Email
        </button>
        ";
            // line 16
            echo $this->getAttribute((isset($context["csrf"]) ? $context["csrf"] : null), "field", array());
            echo "
\t</form>
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
        return array (  55 => 16,  48 => 12,  44 => 11,  40 => 10,  36 => 9,  32 => 7,  30 => 6,  25 => 4,  21 => 2,  19 => 1,);
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
{% elseif flash.getMessage('reg-success') %}
<div class=\"alert alert-success alert-dismissable\" style=\"margin-top: 25px\" align=\"center\">
\t <button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
\t{{ flash.getMessage('reg-success') | first }}
\t<form class=\"form-horizontal\" method=\"POST\" action=\"{{ path_for('auth.resend.token') }}\" role=\"form\">
\t\t<input type=\"hidden\" name=\"token\" value=\"{{ flash.getMessage('token') | first }}\"/>
    \t<input type=\"hidden\" name=\"email\" value=\"{{ flash.getMessage('email') | first }}\"/>
    \t<button class=\"btn btn-danger btn-wide\" type=\"submit\">
              Resend Email
        </button>
        {{ csrf.field | raw }}
\t</form>
</div>
{% endif %}", "partials/alert.twig", "C:\\wamp64\\www\\slim-starter\\resources\\views\\partials\\alert.twig");
    }
}
