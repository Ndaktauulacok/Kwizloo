{{--
    Prof. Fahim, the KwizLoo scholar wolf, drawn as a 400x400 emblem group.
    Include inside an <svg> element. Set $interactive to bind eye tracking to the parent Alpine `mascotHero` scope.
--}}
@php
    $interactive = $interactive ?? false;
    $clipId = 'fahim-seal-'.\Illuminate\Support\Str::random(8);
@endphp
<defs>
    <clipPath id="{{ $clipId }}">
        <circle cx="200" cy="200" r="166"/>
    </clipPath>
    <clipPath id="{{ $clipId }}-eye">
        <path d="M238 188 Q258 170 281 182 Q264 198 238 188 Z"/>
    </clipPath>
</defs>
<g class="fahim">
    <!-- Seal -->
    <circle cx="200" cy="200" r="184" fill="#fbf4ea" stroke="#2a1217" stroke-width="4"/>
    <circle cx="200" cy="200" r="176" fill="none" stroke="#c99a52" stroke-width="3"/>
    <circle cx="200" cy="200" r="166" fill="#761a2d"/>
    <circle cx="200" cy="200" r="156" fill="none" stroke="#e7c98f" stroke-width="1.5" opacity="0.3"/>

    <!-- Sparkles -->
    <g fill="#ecd3a0">
        <path class="fahim-star" d="M76 146 Q76 160 90 160 Q76 160 76 174 Q76 160 62 160 Q76 160 76 146Z"/>
        <path class="fahim-star" style="animation-delay:-1.1s" d="M332 252 Q332 263 343 263 Q332 263 332 274 Q332 263 321 263 Q332 263 332 252Z"/>
        <path class="fahim-star" style="animation-delay:-0.5s" d="M326 138 Q326 145 333 145 Q326 145 326 152 Q326 145 319 145 Q326 145 326 138Z"/>
        <path class="fahim-star" style="animation-delay:-1.7s" d="M64 222 Q64 229 71 229 Q64 229 64 236 Q64 229 57 229 Q64 229 64 222Z"/>
    </g>

    <g clip-path="url(#{{ $clipId }})">
        <!-- Open book behind the bust -->
        <path d="M54 290 Q130 254 200 284 L200 400 L54 400 Z" fill="#e9d6b8"/>
        <path d="M346 290 Q270 254 200 284 L200 400 L346 400 Z" fill="#f3e6d0"/>
        <g fill="none" stroke="#b98a45" stroke-width="2" stroke-linecap="round" opacity="0.45">
            <path d="M74 300 Q132 276 186 296"/>
            <path d="M72 318 Q132 294 186 314"/>
            <path d="M326 300 Q268 276 214 296"/>
            <path d="M328 318 Q268 294 214 314"/>
        </g>

        <!-- Robe -->
        <path d="M58 400 C62 342 108 308 160 300 L246 300 C296 308 340 342 344 400 Z" fill="#5c1222"/>
        <path d="M92 400 C100 356 128 328 166 318 L184 400 Z" fill="#8a2236" opacity="0.65"/>
        <path d="M306 400 C298 358 272 332 240 320 L228 400 Z" fill="#430c19" opacity="0.6"/>
        <path d="M148 302 Q178 320 204 354" fill="none" stroke="#f3e5cf" stroke-width="11" stroke-linecap="round"/>
        <path d="M264 302 Q232 322 206 354" fill="none" stroke="#f3e5cf" stroke-width="11" stroke-linecap="round"/>
    </g>

    <!-- Bust (breathes) -->
    <g class="fahim-bust">
        <!-- Chest ruff -->
        <path d="M156 306 L140 292 L157 286 L143 270 L163 266 L153 250 L175 252 L171 232 L250 236 L264 256 L256 274 L270 287 L252 306 Q204 322 156 306 Z" fill="#f2ecea"/>
        <path d="M172 252 Q214 276 260 252 L250 236 L171 232 Z" fill="#dcd2d3"/>

        <!-- Head (tilts) -->
        <g class="fahim-head">
            <!-- Ears, tucked under the brim -->
            <g class="fahim-ear">
                <path d="M148 200 L120 128 L196 158 Z" fill="#f7f3f1"/>
                <path d="M153 186 L132 140 L182 162 Z" fill="#dcc3c7"/>
            </g>
            <g class="fahim-ear fahim-ear--far">
                <path d="M236 154 L304 122 L292 184 Z" fill="#ebe3e1"/>
                <path d="M250 158 L294 136 L286 174 Z" fill="#cdb2b7"/>
            </g>

            <!-- Head -->
            <path d="M150 172 C160 147 196 134 232 140 C255 144 268 158 278 172 C300 180 322 190 336 200 C346 207 344 220 334 224 C316 234 292 238 272 236 C262 250 246 260 228 264 L214 274 L210 260 L192 272 L190 256 L170 264 L174 248 L152 252 L160 236 L136 234 L150 222 L128 214 L146 204 L130 192 L150 186 Z" fill="#fbf8f6"/>
            <path d="M228 264 L214 274 L210 260 L192 272 L190 256 L170 264 L174 248 L152 252 L160 236 Q200 258 246 246 C240 256 234 260 228 264 Z" fill="#e6dedf"/>

            <!-- Muzzle -->
            <path d="M282 216 C300 222 318 222 334 224 C316 234 292 238 272 236 C276 228 278 222 282 216 Z" fill="#efe8e7"/>
            <path d="M334 225 Q306 238 278 230 Q272 228 268 222" fill="none" stroke="#3a1a20" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            <ellipse cx="335" cy="209" rx="10.5" ry="7.5" transform="rotate(18 335 209)" fill="#26171a"/>
            <ellipse cx="332" cy="206" rx="3" ry="1.8" transform="rotate(18 332 206)" fill="#ffffff" opacity="0.55"/>

            <!-- Brow & eye -->
            <path d="M236 170 Q258 158 284 168" fill="none" stroke="#c4b6b9" stroke-width="3.5" stroke-linecap="round"/>
            <g class="fahim-eye">
                <path d="M238 188 Q258 170 281 182 Q264 198 238 188 Z" fill="#ffffff"/>
                <g clip-path="url(#{{ $clipId }}-eye)">
                    <g @if($interactive) :transform="'translate(' + lookX + ' ' + lookY + ')'" @endif>
                        <circle cx="262" cy="184" r="6.6" fill="#d11a2f"/>
                        <circle cx="262" cy="184" r="6.6" fill="none" stroke="#8c0f1f" stroke-width="1.2"/>
                        <circle cx="262" cy="184" r="2.8" fill="#1d0e11"/>
                        <circle cx="264.6" cy="181.4" r="1.6" fill="#ffffff"/>
                    </g>
                </g>
                <path d="M238 188 Q258 170 281 182 Q264 198 238 188 Z" fill="none" stroke="#2a1217" stroke-width="1.2" opacity="0.5"/>
                <path d="M238 188 Q258 170 281 182" fill="none" stroke="#2a1217" stroke-width="3" stroke-linecap="round"/>
            </g>
            <ellipse cx="248" cy="212" rx="11" ry="6" fill="#e7b3bb" opacity="0.45"/>

            <!-- Mortarboard -->
            <path d="M174 158 Q222 132 274 152 L272 124 Q222 106 176 128 Z" fill="#5c1222"/>
            <path d="M100 110 L222 62 L336 100 L222 142 Z" fill="#8f2337"/>
            <path d="M100 110 L222 142 L336 100 L336 108 L222 151 L100 118 Z" fill="#3f0b17"/>
            <path d="M100 110 L222 62 L336 100" fill="none" stroke="#b13a50" stroke-width="2" stroke-linejoin="round" opacity="0.8"/>
            <path d="M222 101 L110 112" fill="none" stroke="#d4ab63" stroke-width="3" stroke-linecap="round"/>
            <circle cx="222" cy="101" r="6" fill="#d4ab63" stroke="#a97f3a" stroke-width="1.5"/>
            <g class="fahim-tassel">
                <path d="M110 112 L110 152" fill="none" stroke="#d4ab63" stroke-width="3" stroke-linecap="round"/>
                <circle cx="110" cy="155" r="6" fill="#e0bb73" stroke="#a97f3a" stroke-width="1.5"/>
                <path d="M102 160 L118 160 L124 198 L96 198 Z" fill="#e0bb73"/>
                <g stroke="#a97f3a" stroke-width="1.5" stroke-linecap="round">
                    <path d="M105 164 L102 196"/><path d="M110 164 L110 196"/><path d="M115 164 L118 196"/>
                </g>
            </g>
        </g>
    </g>

    <!-- Brooch -->
    <circle cx="205" cy="356" r="13" fill="#d4ab63" stroke="#a97f3a" stroke-width="2"/>
    <circle cx="201" cy="352" r="4" fill="#f4dfb0"/>
</g>
