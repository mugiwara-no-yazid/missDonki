@if ($paginator->hasPages())
<nav style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">

    {{-- Info pages --}}
    <div style="font-size:12px;color:var(--gris3);">
        Affichage
        <span style="color:var(--blanc);">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</span>
        sur
        <span style="color:var(--blanc);">{{ $paginator->total() }}</span>
        résultat(s)
    </div>

    {{-- Boutons --}}
    <div style="display:flex;gap:5px;align-items:center;flex-wrap:wrap;">

        {{-- Précédent --}}
        @if ($paginator->onFirstPage())
            <span style="padding:6px 12px;border-radius:6px;font-size:13px;border:1px solid var(--gris1);
                          color:var(--gris2);cursor:not-allowed;user-select:none;">
                ‹
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               style="padding:6px 12px;border-radius:6px;font-size:13px;border:1px solid var(--gris1);
                      color:#aaa;text-decoration:none;transition:all .15s;"
               onmouseover="this.style.borderColor='var(--or)';this.style.color='var(--or)'"
               onmouseout="this.style.borderColor='var(--gris1)';this.style.color='#aaa'">
                ‹
            </a>
        @endif

        {{-- Pages numérotées --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="padding:6px 8px;font-size:13px;color:var(--gris3);">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="padding:6px 12px;border-radius:6px;font-size:13px;
                                     background:var(--or);color:var(--noir);
                                     border:1px solid var(--or);font-weight:500;">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           style="padding:6px 12px;border-radius:6px;font-size:13px;
                                  border:1px solid var(--gris1);color:#aaa;text-decoration:none;transition:all .15s;"
                           onmouseover="this.style.borderColor='var(--or)';this.style.color='var(--or)'"
                           onmouseout="this.style.borderColor='var(--gris1)';this.style.color='#aaa'">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Suivant --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               style="padding:6px 12px;border-radius:6px;font-size:13px;border:1px solid var(--gris1);
                      color:#aaa;text-decoration:none;transition:all .15s;"
               onmouseover="this.style.borderColor='var(--or)';this.style.color='var(--or)'"
               onmouseout="this.style.borderColor='var(--gris1)';this.style.color='#aaa'">
                ›
            </a>
        @else
            <span style="padding:6px 12px;border-radius:6px;font-size:13px;border:1px solid var(--gris1);
                          color:var(--gris2);cursor:not-allowed;user-select:none;">
                ›
            </span>
        @endif

    </div>
</nav>
@endif