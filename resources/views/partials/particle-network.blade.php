{{-- Sieć "neuronów" w tle — subtelny, animowany canvas (particle/
     constellation effect), reagujący na ruch myszy. Nieinteraktywny
     (pointer-events: none), rysowany w resources/js/app.js, wyłączany
     przy prefers-reduced-motion. Widoczny tam, gdzie sekcje nie mają
     własnego, pełnego tła (patrz .section vs .section--alt w app.css) —
     te same miejsca, w których dziś widać siatkę w tle. --}}
<canvas class="particle-network" aria-hidden="true"></canvas>
