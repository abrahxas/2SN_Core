<?php

/* @WebProfiler/Profiler/toolbar_js.html.twig */
class __TwigTemplate_8febb7a4a3cfc0c397b4bc94d40f265c9bb88cb05d2ddc5a7e2a901ffe4754ac extends Twig_Template
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
        echo "<div id=\"sfwdt";
        echo twig_escape_filter($this->env, (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")), "html", null, true);
        echo "\" class=\"sf-toolbar\" style=\"display: none\"></div>
";
        // line 2
        $this->env->loadTemplate("@WebProfiler/Profiler/base_js.html.twig")->display($context);
        // line 3
        echo "<script>/*<![CDATA[*/
    (function () {
        ";
        // line 5
        if (("top" == (isset($context["position"]) ? $context["position"] : $this->getContext($context, "position")))) {
            // line 6
            echo "            var sfwdt = document.getElementById('sfwdt";
            echo twig_escape_filter($this->env, (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")), "html", null, true);
            echo "');
            document.body.insertBefore(
                document.body.removeChild(sfwdt),
                document.body.firstChild
            );
        ";
        }
        // line 12
        echo "
        Sfjs.load(
            'sfwdt";
        // line 14
        echo twig_escape_filter($this->env, (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")), "html", null, true);
        echo "',
            '";
        // line 15
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("_wdt", array("token" => (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")))), "html", null, true);
        echo "',
            function(xhr, el) {
                el.style.display = -1 !== xhr.responseText.indexOf('sf-toolbarreset') ? 'block' : 'none';

                if (el.style.display == 'none') {
                    return;
                }

                if (Sfjs.getPreference('toolbar/displayState') == 'none') {
                    document.getElementById('sfToolbarMainContent-";
        // line 24
        echo twig_escape_filter($this->env, (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")), "html", null, true);
        echo "').style.display = 'none';
                    document.getElementById('sfToolbarClearer-";
        // line 25
        echo twig_escape_filter($this->env, (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")), "html", null, true);
        echo "').style.display = 'none';
                    document.getElementById('sfMiniToolbar-";
        // line 26
        echo twig_escape_filter($this->env, (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")), "html", null, true);
        echo "').style.display = 'block';
                } else {
                    document.getElementById('sfToolbarMainContent-";
        // line 28
        echo twig_escape_filter($this->env, (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")), "html", null, true);
        echo "').style.display = 'block';
                    document.getElementById('sfToolbarClearer-";
        // line 29
        echo twig_escape_filter($this->env, (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")), "html", null, true);
        echo "').style.display = 'block';
                    document.getElementById('sfMiniToolbar-";
        // line 30
        echo twig_escape_filter($this->env, (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")), "html", null, true);
        echo "').style.display = 'none';
                }
            },
            function(xhr) {
                if (xhr.status !== 0) {
                    confirm('An error occurred while loading the web debug toolbar (' + xhr.status + ': ' + xhr.statusText + ').\\n\\nDo you want to open the profiler?') && (window.location = '";
        // line 35
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("_profiler", array("token" => (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")))), "html", null, true);
        echo "');
                }
            },
            {'maxTries': 5}
        );
    })();
/*]]>*/</script>
";
    }

    public function getTemplateName()
    {
        return "@WebProfiler/Profiler/toolbar_js.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  83 => 30,  75 => 28,  70 => 26,  50 => 15,  42 => 12,  32 => 6,  30 => 5,  26 => 3,  24 => 2,  1038 => 312,  1029 => 311,  1026 => 310,  1012 => 307,  1006 => 306,  1003 => 305,  981 => 302,  956 => 301,  953 => 300,  945 => 296,  941 => 295,  938 => 294,  929 => 287,  927 => 286,  923 => 285,  920 => 284,  915 => 280,  907 => 278,  903 => 277,  901 => 276,  899 => 275,  896 => 274,  890 => 271,  887 => 270,  883 => 267,  880 => 265,  878 => 264,  875 => 263,  868 => 259,  866 => 258,  844 => 257,  841 => 255,  838 => 253,  836 => 252,  834 => 251,  831 => 250,  827 => 247,  825 => 246,  823 => 245,  820 => 244,  816 => 239,  813 => 238,  809 => 235,  807 => 234,  805 => 233,  802 => 232,  798 => 229,  796 => 228,  794 => 227,  792 => 226,  790 => 225,  787 => 224,  783 => 221,  780 => 216,  775 => 212,  755 => 208,  752 => 206,  750 => 205,  747 => 203,  745 => 202,  742 => 200,  740 => 199,  738 => 198,  735 => 197,  731 => 192,  729 => 191,  726 => 190,  722 => 187,  720 => 186,  717 => 185,  707 => 182,  704 => 180,  702 => 179,  699 => 178,  695 => 175,  693 => 174,  690 => 173,  686 => 170,  684 => 169,  681 => 168,  677 => 165,  675 => 164,  672 => 163,  667 => 160,  665 => 159,  662 => 158,  658 => 155,  656 => 154,  653 => 153,  649 => 150,  647 => 149,  644 => 148,  640 => 145,  637 => 144,  633 => 141,  631 => 140,  628 => 139,  624 => 136,  622 => 135,  619 => 133,  614 => 129,  604 => 128,  599 => 127,  597 => 126,  594 => 124,  592 => 123,  589 => 122,  584 => 118,  582 => 116,  581 => 115,  580 => 114,  579 => 113,  575 => 112,  572 => 110,  570 => 109,  567 => 108,  562 => 104,  560 => 103,  558 => 102,  556 => 101,  554 => 100,  550 => 99,  547 => 97,  545 => 96,  542 => 95,  528 => 92,  525 => 91,  511 => 88,  508 => 87,  483 => 82,  480 => 80,  478 => 79,  476 => 78,  471 => 77,  469 => 76,  452 => 75,  449 => 74,  445 => 71,  443 => 70,  441 => 69,  435 => 66,  433 => 65,  431 => 64,  429 => 63,  427 => 62,  418 => 60,  416 => 59,  408 => 58,  405 => 57,  402 => 56,  400 => 55,  397 => 54,  393 => 51,  387 => 49,  385 => 48,  381 => 47,  376 => 46,  373 => 45,  368 => 41,  365 => 39,  363 => 38,  360 => 37,  352 => 34,  349 => 33,  345 => 30,  342 => 28,  340 => 27,  337 => 26,  333 => 23,  331 => 22,  329 => 21,  326 => 19,  324 => 18,  320 => 17,  317 => 16,  303 => 13,  301 => 12,  298 => 11,  293 => 7,  290 => 5,  288 => 4,  285 => 3,  281 => 310,  278 => 309,  276 => 305,  273 => 304,  271 => 300,  268 => 299,  266 => 294,  263 => 293,  260 => 291,  258 => 284,  255 => 283,  253 => 274,  250 => 273,  248 => 270,  245 => 269,  243 => 263,  240 => 262,  238 => 250,  235 => 249,  233 => 244,  230 => 243,  227 => 241,  225 => 238,  222 => 237,  220 => 232,  217 => 231,  215 => 224,  212 => 223,  210 => 216,  207 => 215,  204 => 213,  202 => 212,  199 => 211,  197 => 197,  194 => 196,  191 => 194,  189 => 190,  186 => 189,  184 => 185,  181 => 184,  179 => 178,  176 => 177,  174 => 173,  171 => 172,  169 => 168,  166 => 167,  164 => 163,  161 => 162,  159 => 158,  156 => 157,  154 => 153,  151 => 152,  149 => 148,  146 => 147,  144 => 144,  141 => 143,  139 => 139,  136 => 138,  134 => 133,  131 => 132,  129 => 122,  126 => 121,  124 => 108,  121 => 107,  119 => 95,  116 => 94,  114 => 91,  111 => 90,  109 => 87,  106 => 86,  104 => 74,  101 => 73,  99 => 54,  96 => 53,  94 => 45,  89 => 37,  86 => 36,  84 => 33,  79 => 29,  76 => 25,  74 => 16,  71 => 15,  69 => 11,  66 => 25,  64 => 3,  61 => 2,  33 => 6,  22 => 3,  19 => 1,  103 => 28,  100 => 27,  93 => 29,  91 => 35,  87 => 25,  81 => 32,  72 => 21,  67 => 20,  62 => 24,  58 => 18,  54 => 16,  46 => 14,  40 => 11,  36 => 10,  29 => 8,  20 => 1,  31 => 9,  28 => 4,);
    }
}
