{{--
    Landing hero scene: Syed the scholar wolf with floating study cards.
    Rendered inside the `mascotHero` Alpine scope (uses lookX/lookY, isWiggling).
--}}
<svg viewBox="0 0 560 500" class="syed-scene w-full h-auto overflow-visible" role="img" aria-labelledby="syed-scene-title">
    <title id="syed-scene-title">Syed, serigala sarjana KwizLoo, memakai topi graduasi dengan kad pembelajaran terapung di sekelilingnya</title>

    <!-- Ground shadow -->
    <ellipse class="syed-shadow" cx="280" cy="478" rx="150" ry="12" fill="#2a1217" opacity="0.12"/>

    <!-- Emblem -->
    <g class="syed-float">
        <g transform="translate(80 58)">
            <g class="syed-pet" :class="{ 'is-petted': isWiggling }">
                @include('partials.syed-emblem', ['interactive' => true])
            </g>
        </g>
    </g>

    <!-- Floating card, left: speed quiz -->
    <g class="syed-card">
        <g transform="rotate(-8 92 150)">
            <rect x="9" y="105" width="168" height="92" rx="14" fill="#2a1217"/>
            <rect x="4" y="100" width="168" height="92" rx="14" fill="#fbf4ea" stroke="#2a1217" stroke-width="3.5"/>
            <text x="20" y="126" font-family="Bricolage Grotesque, Instrument Sans, sans-serif" font-size="11" font-weight="700" letter-spacing="1.2" fill="#761a2d">UJIAN PANTAS</text>
            <text x="20" y="160" font-family="Bricolage Grotesque, Instrument Sans, sans-serif" font-size="29" font-weight="800" fill="#2a1217">00:42</text>
            <rect x="20" y="170" width="136" height="10" rx="5" fill="#efe0c8" stroke="#2a1217" stroke-width="2.5"/>
            <rect class="syed-timer" x="20" y="170" width="88" height="10" rx="5" fill="#761a2d"/>
        </g>
    </g>

    <!-- Floating card, right: true or false -->
    <g class="syed-card" style="animation-delay:-2.2s">
        <g transform="rotate(7 466 380)">
            <rect x="387" y="335" width="172" height="100" rx="14" fill="#2a1217"/>
            <rect x="382" y="330" width="172" height="100" rx="14" fill="#ffffff" stroke="#2a1217" stroke-width="3.5"/>
            <text x="398" y="354" font-family="Bricolage Grotesque, Instrument Sans, sans-serif" font-size="10.5" font-weight="700" letter-spacing="1.2" fill="#761a2d">BETUL ATAU SALAH?</text>
            <text x="398" y="376" font-family="Newsreader, Georgia, serif" font-size="14.5" font-style="italic" fill="#2a1217">“Klorofil menyerap</text>
            <text x="398" y="393" font-family="Newsreader, Georgia, serif" font-size="14.5" font-style="italic" fill="#2a1217">cahaya hijau.”</text>
            <rect x="398" y="402" width="64" height="19" rx="6" fill="#ffffff" stroke="#2a1217" stroke-width="2.5"/>
            <text x="430" y="415.5" text-anchor="middle" font-family="Bricolage Grotesque, Instrument Sans, sans-serif" font-size="10.5" font-weight="700" fill="#2a1217">Betul</text>
            <rect x="470" y="402" width="64" height="19" rx="6" fill="#761a2d" stroke="#2a1217" stroke-width="2.5"/>
            <text x="502" y="415.5" text-anchor="middle" font-family="Bricolage Grotesque, Instrument Sans, sans-serif" font-size="10.5" font-weight="700" fill="#fbf4ea">Salah ✓</text>
        </g>
    </g>

    <!-- Floating seal, top right: grade -->
    <g class="syed-card" style="animation-delay:-1.1s">
        <g transform="rotate(10 486 92)">
            <circle cx="490" cy="96" r="34" fill="#2a1217"/>
            <circle cx="486" cy="92" r="34" fill="#d4ab63" stroke="#2a1217" stroke-width="3.5"/>
            <circle cx="486" cy="92" r="26" fill="none" stroke="#2a1217" stroke-width="1.5" stroke-dasharray="3 4" opacity="0.5"/>
            <text x="486" y="101" text-anchor="middle" font-family="Bricolage Grotesque, Instrument Sans, sans-serif" font-size="25" font-weight="800" fill="#2a1217">A+</text>
        </g>
    </g>
</svg>
