import re

with open('resources/views/settings/generalsettings.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove @media screen and (min-width: 992px) { ... } for mobile steps
css_to_remove = r"""        @media screen and \(min-width: 992px\) \{
            \.mobile-steps,
            \.mobile-nav-btns \{
                display: none !important;
            \}

            \.step-content \{
                display: flex !important;
            \}

            #shop-settings:not\(\.active\) \{
                display: none !important;
            \}

            #shop-settings\.active \{
                display: flex;
                flex-wrap: wrap;
            \}

            #shop-settings \.step-content \{
                display: contents !important;
            \}

            #shop-settings \.desktop-submit \{
                width: 100%;
                margin-top: 8px;
            \}
        \}"""
content = re.sub(css_to_remove, '', content)

# 2. Change @media screen and (max-width: 991px) to just be normal rules
content = content.replace("        @media screen and (max-width: 991px) {\n            .step-content {", "        /* Modern Multi-step Form Styles */\n        .step-content {")

# Find the ending bracket of the @media query (around line 238 before the min-width)
content = re.sub(r"            \}\n        \}\n\n$", r"            }\n\n", content, flags=re.MULTILINE)

with open('resources/views/settings/generalsettings.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
