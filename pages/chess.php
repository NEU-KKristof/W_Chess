<!--
Ez a fájl tartalmazza a feltöltött index3.html tartalmát,
de a <html>, <head>, <body> tagek NÉLKÜL,
mert azokat már a header.php és footer.php biztosítja.
A stílusok és a JS is átkerült a header.php-ba, hogy globálisan elérhető legyen.
DE a jobb átláthatóság kedvéért a JS-t itt hagyom,
viszont a stílusokat a header-be tettem.
-->

<h2>Online Sakk</h2>
<p>Játssz egy partit! A meccsek mentése hamarosan beépítésre kerül...</p>

<!-- Játék vége felugró ablak -->
<div id="jatekVegeOverlay">
    <div id="jatekVegeModal">
        <h2 id="jatekVegeUzenet"></h2>
        <!-- 
          FONTOS: Az eredeti `init()` helyett `location.reload()`-ot használunk,
          hogy a JS állapot (bábuk pozíciója) biztosan nullázódjon.
        -->
        <button onclick="location.reload()">Új Játék</button>
    </div>
</div>

<!-- A sakktábla helye -->
<table>
    <tbody id="sakktabla"></tbody>
</table>

<!-- 
  TODO: A jövőben ezt a scriptet össze kell kötni a `saveMatch()` funkcióval,
  hogy a játék végén elmentse az eredményt (pl. AJAX hívással).
-->

<script>
    // --- A FELTÖLTÖTT index3.html TELJES JAVASCRIPT KÓDJA ---
    // (Itt kezdődik a hosszú JS kód a bábukkal és a logikával)

    let kilep = true; // true: fehér lép, false: fekete lép
    let kijeloltBabu = null; // A jelenleg kiválasztott bábu objektuma
    let lehetsegesLepesek = []; // A kijelölt bábu érvényes lépéseinek tömbje
    let jatekAktiv = true; // A játék véget ért-e

    // --- BÁBÚK DEFINÍCIÓJA ---
    const feherbabuk = [
        { nev: "feher_paraszt1", el: true, poz: [2, 1], karakter: "P", kinezet: "♙", mozgott: false },
        { nev: "feher_paraszt2", el: true, poz: [2, 2], karakter: "P", kinezet: "♙", mozgott: false },
        { nev: "feher_paraszt3", el: true, poz: [2, 3], karakter: "P", kinezet: "♙", mozgott: false },
        { nev: "feher_paraszt4", el: true, poz: [2, 4], karakter: "P", kinezet: "♙", mozgott: false },
        { nev: "feher_paraszt5", el: true, poz: [2, 5], karakter: "P", kinezet: "♙", mozgott: false },
        { nev: "feher_paraszt6", el: true, poz: [2, 6], karakter: "P", kinezet: "♙", mozgott: false },
        { nev: "feher_paraszt7", el: true, poz: [2, 7], karakter: "P", kinezet: "♙", mozgott: false },
        { nev: "feher_paraszt8", el: true, poz: [2, 8], karakter: "P", kinezet: "♙", mozgott: false },
        { nev: "feher_bastya1", el: true, poz: [1, 1], karakter: "B", kinezet: "♖", mozgott: false },
        { nev: "feher_bastya2", el: true, poz: [1, 8], karakter: "B", kinezet: "♖", mozgott: false },
        { nev: "feher_csiko1", el: true, poz: [1, 2], karakter: "CS", kinezet: "♘" },
        { nev: "feher_csiko2", el: true, poz: [1, 7], karakter: "CS", kinezet: "♘" },
        { nev: "feher_futo1", el: true, poz: [1, 3], karakter: "F", kinezet: "♗" },
        { nev: "feher_futo2", el: true, poz: [1, 6], karakter: "F", kinezet: "♗" },
        { nev: "feher_kiralyno", el: true, poz: [1, 4], karakter: "KN", kinezet: "♕" },
        { nev: "feher_kiraly", el: true, poz: [1, 5], karakter: "K", kinezet: "♔", mozgott: false }
    ];

    const feketebabuk = [
        { nev: "fekete_paraszt1", el: true, poz: [7, 1], karakter: "p", kinezet: "♟", mozgott: false },
        { nev: "fekete_paraszt2", el: true, poz: [7, 2], karakter: "p", kinezet: "♟", mozgott: false },
        { nev: "fekete_paraszt3", el: true, poz: [7, 3], karakter: "p", kinezet: "♟", mozgott: false },
        { nev: "fekete_paraszt4", el: true, poz: [7, 4], karakter: "p", kinezet: "♟", mozgott: false },
        { nev: "fekete_paraszt5", el: true, poz: [7, 5], karakter: "p", kinezet: "♟", mozgott: false },
        { nev: "fekete_paraszt6", el: true, poz: [7, 6], karakter: "p", kinezet: "♟", mozgott: false },
        { nev: "fekete_paraszt7", el: true, poz: [7, 7], karakter: "p", kinezet: "♟", mozgott: false },
        { nev: "fekete_paraszt8", el: true, poz: [7, 8], karakter: "p", kinezet: "♟", mozgott: false },
        { nev: "fekete_bastya1", el: true, poz: [8, 1], karakter: "b", kinezet: "♜", mozgott: false },
        { nev: "fekete_bastya2", el: true, poz: [8, 8], karakter: "b", kinezet: "♜", mozgott: false },
        { nev: "fekete_csiko1", el: true, poz: [8, 2], karakter: "cs", kinezet: "♞" },
        { nev: "fekete_csiko2", el: true, poz: [8, 7], karakter: "cs", kinezet: "♞" },
        { nev: "fekete_futo1", el: true, poz: [8, 3], karakter: "f", kinezet: "♝" },
        { nev: "fekete_futo2", el: true, poz: [8, 6], karakter: "f", kinezet: "♝" },
        { nev: "fekete_kiralyno", el: true, poz: [8, 4], karakter: "kn", kinezet: "♛" },
        { nev: "fekete_kiraly", el: true, poz: [8, 5], karakter: "k", kinezet: "♚", mozgott: false }
    ];

    // --- INICIALIZÁLÁS ---
    // Az 'init' függvényt most közvetlenül futtatjuk, amint betöltődik az oldal.
    (function init() {
        // Reset state
        kilep = true;
        kijeloltBabu = null;
        lehetsegesLepesek = [];
        jatekAktiv = true;
        
        const overlay = document.getElementById('jatekVegeOverlay');
        if (overlay) overlay.style.display = 'none';
        
        tablaGeneralas();
        render();

        // Gomb eseménykezelőjének biztosítása
        const ujJatekGomb = document.querySelector("#jatekVegeModal button");
        if (ujJatekGomb) {
             ujJatekGomb.setAttribute("onclick", "location.reload()");
        }
    })();


    // --- TÁBLA MEGJELENÍTÉSE (RENDER) ---
    function tablaGeneralas() {
        const tabla = document.getElementById('sakktabla');
        if (!tabla) return; // Ha valamiért nincs tábla, ne haljon el
        
        tabla.innerHTML = ''; // Tábla törlése újrarajzolás előtt
        for (let i = 8; i >= 1; i--) {
            const sor = document.createElement('tr');
            for (let j = 1; j <= 8; j++) {
                const cella = document.createElement('td');
                cella.id = JSON.stringify([i, j]);
                
                if ((i + j) % 2 === 0) {
                    cella.style.backgroundColor = "gray";
                } else {
                    cella.style.backgroundColor = "wheat";
                }
                
                cella.onclick = function() { kattintas(this); };
                sor.appendChild(cella);
            }
            tabla.appendChild(sor);
        }
    }

    function render() {
        const cellak = document.querySelectorAll('#sakktabla td');
        if (cellak.length === 0) tablaGeneralas(); // Ha a cellák még nincsenek, generáljuk
        
        document.querySelectorAll('#sakktabla td').forEach(cella => cella.innerHTML = '');

        feherbabuk.forEach(b => {
            if (b.el) {
                const cella = document.getElementById(JSON.stringify(b.poz));
                if (cella) cella.innerHTML = b.kinezet;
            }
        });

        feketebabuk.forEach(b => {
            if (b.el) {
                const cella = document.getElementById(JSON.stringify(b.poz));
                if (cella) cella.innerHTML = b.kinezet;
            }
        });
    }

    // --- JÁTÉK LOGIKA SEGÉDFÜGGVÉNYEK ---

    function getBabuAtPoz(poz) {
        const pozString = JSON.stringify(poz);
        let babu = feherbabuk.find(b => b.el && JSON.stringify(b.poz) === pozString);
        if (babu) return { babu: babu, szin: 'feher' };
        babu = feketebabuk.find(b => b.el && JSON.stringify(b.poz) === pozString);
        if (babu) return { babu: babu, szin: 'fekete' };
        return null;
    }

    function isSajatBabu(babuInfo) {
        if (!babuInfo) return false;
        return (kilep && babuInfo.szin === 'feher') || (!kilep && babuInfo.szin === 'fekete');
    }

    function removeHighlights() {
        document.querySelectorAll('.lehetseges-lepes').forEach(c => c.classList.remove('lehetseges-lepes'));
        document.querySelectorAll('.kijelolt-cella').forEach(c => c.classList.remove('kijelolt-cella'));
        document.querySelectorAll('.sakkban-cella').forEach(c => c.classList.remove('sakkban-cella'));
    }

    function highlightLepesek(lepesek) {
        lepesek.forEach(poz => {
            const cella = document.getElementById(JSON.stringify(poz));
            if (cella) cella.classList.add('lehetseges-lepes');
        });
    }

    function isPozicioValid(poz) {
        return poz[0] >= 1 && poz[0] <= 8 && poz[1] >= 1 && poz[1] <= 8;
    }

    function getCellaAllapota(poz) {
        if (!isPozicioValid(poz)) return 'ervenytelen';
        const talalat = getBabuAtPoz(poz);
        if (!talalat) return 'ures';
        if (isSajatBabu(talalat)) return 'barat';
        return 'ellenseg';
    }

    function getKiralyPoz(szin) {
        if (szin === 'feher') {
            return feherbabuk.find(b => b.karakter === 'K').poz;
        } else {
            return feketebabuk.find(b => b.karakter === 'k').poz;
        }
    }

    /** Ellenőrzi, hogy egy adott pozíció támadás alatt áll-e az adott színű játékos által. */
    function isPozicioTamadva(poz, tamadoSzin) {
        const ellensegesBabuk = (tamadoSzin === 'feher') ? feherbabuk : feketebabuk;
        const pozString = JSON.stringify(poz);

        for (const babuInfo of ellensegesBabuk.filter(b => b.el)) {
            const karakter = babuInfo.karakter.toUpperCase();
            const babuPoz = babuInfo.poz;

            switch (karakter) {
                case 'P':
                    const irany = (tamadoSzin === 'feher') ? 1 : -1;
                    if (JSON.stringify([babuPoz[0] + irany, babuPoz[1] + 1]) === pozString) return true;
                    if (JSON.stringify([babuPoz[0] + irany, babuPoz[1] - 1]) === pozString) return true;
                    break;
                case 'CS':
                    const csikoMozgasok = [[2, 1], [2, -1], [-2, 1], [-2, -1], [1, 2], [1, -2], [-1, 2], [-1, -2]];
                    for (const m of csikoMozgasok) {
                        if (JSON.stringify([babuPoz[0] + m[0], babuPoz[1] + m[1]]) === pozString) return true;
                    }
                    break;
                case 'K':
                    const kiralyMozgasok = [[1, 0], [-1, 0], [0, 1], [0, -1], [1, 1], [1, -1], [-1, 1], [-1, -1]];
                    for (const m of kiralyMozgasok) {
                        if (JSON.stringify([babuPoz[0] + m[0], babuPoz[1] + m[1]]) === pozString) return true;
                    }
                    break;
                case 'B':
                case 'F':
                case 'KN':
                    let iranyok = [];
                    if (karakter !== 'F') iranyok.push(...[[1, 0], [-1, 0], [0, 1], [0, -1]]); // Bástya, Királynő
                    if (karakter !== 'B') iranyok.push(...[[1, 1], [1, -1], [-1, 1], [-1, -1]]); // Futó, Királynő
                    
                    for (const irany of iranyok) {
                        for (let i = 1; i <= 7; i++) {
                            const ujPoz = [babuPoz[0] + i * irany[0], babuPoz[1] + i * irany[1]];
                            if (!isPozicioValid(ujPoz)) break;
                            if (JSON.stringify(ujPoz) === pozString) return true;
                            if (getBabuAtPoz(ujPoz) !== null) break; // Blokkolva
                        }
                    }
                    break;
            }
        }
        return false;
    }

    /** Igaz, ha a megadott színű király sakkban van. */
    function isKiralySakkban(sajatSzin) {
        const kiralyPoz = getKiralyPoz(sajatSzin);
        const ellensegSzin = (sajatSzin === 'feher') ? 'fekete' : 'feher';
        return isPozicioTamadva(kiralyPoz, ellensegSzin);
    }

    // --- LÉPÉS KALKULÁCIÓS FÜGGVÉNYEK (PSZEUDO-LEGÁLIS) ---
    // ... (A teljes lépéskalkulációs logika innen) ...
    function calculateParasztLepesek(babuInfo) {
        const lepesek = [];
        const babu = babuInfo.babu;
        const poz = babu.poz;
        const irany = (babuInfo.szin === 'feher') ? 1 : -1;
        const elore1 = [poz[0] + irany, poz[1]];
        if (getCellaAllapota(elore1) === 'ures') {
            lepesek.push(elore1);
            if (babu.mozgott === false) {
                const elore2 = [poz[0] + (2 * irany), poz[1]];
                if (getCellaAllapota(elore2) === 'ures') {
                    lepesek.push(elore2);
                }
            }
        }
        const ut1 = [poz[0] + irany, poz[1] + 1];
        if (getCellaAllapota(ut1) === 'ellenseg') {
            lepesek.push(ut1);
        }
        const ut2 = [poz[0] + irany, poz[1] - 1];
        if (getCellaAllapota(ut2) === 'ellenseg') {
            lepesek.push(ut2);
        }
        return lepesek;
    }
    function calculateCsikoLepesek(babuInfo) {
        const lepesek = [];
        const poz = babuInfo.babu.poz;
        const mozgasok = [[2, 1], [2, -1], [-2, 1], [-2, -1], [1, 2], [1, -2], [-1, 2], [-1, -2]];
        mozgasok.forEach(m => {
            const ujPoz = [poz[0] + m[0], poz[1] + m[1]];
            const allapot = getCellaAllapota(ujPoz);
            if (allapot === 'ures' || allapot === 'ellenseg') {
                lepesek.push(ujPoz);
            }
        });
        return lepesek;
    }
    function calculateFutoLepesek(babuInfo) {
        const lepesek = [];
        const poz = babuInfo.babu.poz;
        const iranyok = [[1, 1], [1, -1], [-1, 1], [-1, -1]];
        iranyok.forEach(irany => {
            for (let i = 1; i <= 7; i++) {
                const ujPoz = [poz[0] + i * irany[0], poz[1] + i * irany[1]];
                const allapot = getCellaAllapota(ujPoz);
                if (allapot === 'ures') lepesek.push(ujPoz);
                else if (allapot === 'ellenseg') { lepesek.push(ujPoz); break; }
                else break;
            }
        });
        return lepesek;
    }
    function calculateBastyaLepesek(babuInfo) {
        const lepesek = [];
        const poz = babuInfo.babu.poz;
        const iranyok = [[1, 0], [-1, 0], [0, 1], [0, -1]];
        iranyok.forEach(irany => {
            for (let i = 1; i <= 7; i++) {
                const ujPoz = [poz[0] + i * irany[0], poz[1] + i * irany[1]];
                const allapot = getCellaAllapota(ujPoz);
                if (allapot === 'ures') lepesek.push(ujPoz);
                else if (allapot === 'ellenseg') { lepesek.push(ujPoz); break; }
                else break;
            }
        });
        return lepesek;
    }
    function calculateKiralynoLepesek(babuInfo) {
        return calculateBastyaLepesek(babuInfo).concat(calculateFutoLepesek(babuInfo));
    }
    function calculateKiralyLepesek(babuInfo) {
        const lepesek = [];
        const poz = babuInfo.babu.poz;
        const mozgasok = [[1, 0], [-1, 0], [0, 1], [0, -1], [1, 1], [1, -1], [-1, 1], [-1, -1]];
        mozgasok.forEach(m => {
            const ujPoz = [poz[0] + m[0], poz[1] + m[1]];
            const allapot = getCellaAllapota(ujPoz);
            if (allapot === 'ures' || allapot === 'ellenseg') {
                lepesek.push(ujPoz);
            }
        });
        return lepesek;
    }
    function getPszeudoLepesek(babuInfo) {
        const karakter = babuInfo.babu.karakter.toUpperCase();
        switch (karakter) {
            case 'P': return calculateParasztLepesek(babuInfo);
            case 'CS': return calculateCsikoLepesek(babuInfo);
            case 'F': return calculateFutoLepesek(babuInfo);
            case 'B': return calculateBastyaLepesek(babuInfo);
            case 'KN': return calculateKiralynoLepesek(babuInfo);
            case 'K': return calculateKiralyLepesek(babuInfo);
            default: return [];
        }
    }

    /** Kiszámolja egy bábu összes VALÓDI legális lépését, figyelembe véve a sakkot. */
    function szabalyosLepesek(babuInfo) {
        const pszeudoLepesek = getPszeudoLepesek(babuInfo);
        const veglegesLepesek = [];
        const sajatSzin = babuInfo.szin;
        for (const lepes of pszeudoLepesek) {
            const eredetiPoz = babuInfo.babu.poz;
            const celBabuInfo = getBabuAtPoz(lepes);
            babuInfo.babu.poz = lepes;
            if (celBabuInfo) celBabuInfo.babu.el = false;
            if (!isKiralySakkban(sajatSzin)) {
                veglegesLepesek.push(lepes);
            }
            babuInfo.babu.poz = eredetiPoz;
            if (celBabuInfo) celBabuInfo.babu.el = true;
        }
        return veglegesLepesek;
    }

    // --- LÉPÉS VÉGREHAJTÁSA ---
    function lepesVegrehajtasa(babuInfo, ujPoz) {
        const celBabuInfo = getBabuAtPoz(ujPoz);
        if (celBabuInfo && !isSajatBabu(celBabuInfo)) {
            celBabuInfo.babu.el = false;
        }
        babuInfo.babu.poz = ujPoz;
        const karakter = babuInfo.babu.karakter.toUpperCase();
        if (karakter === 'P' || karakter === 'K' || karakter === 'B') {
            babuInfo.babu.mozgott = true;
        }
        GyalogBeeres();
        kilep = !kilep;
        kijeloltBabu = null;
        lehetsegesLepesek = [];
        render();
        checkJatekVege();
    }

    function checkJatekVege() {
        const kovetkezoJatekosSzin = kilep ? 'feher' : 'fekete';
        const kovetkezoJatekosBabui = kilep ? feherbabuk : feketebabuk;
        let vanLegalabbEgyLepes = false;
        for (const babu of kovetkezoJatekosBabui.filter(b => b.el)) {
            const lepesek = szabalyosLepesek({ babu: babu, szin: kovetkezoJatekosSzin });
            if (lepesek.length > 0) {
                vanLegalabbEgyLepes = true;
                break;
            }
        }
        if (!vanLegalabbEgyLepes) {
            if (isKiralySakkban(kovetkezoJatekosSzin)) {
                const nyertes = kilep ? "Fekete" : "Fehér";
                jatekVegeUzenet(`Sakk-Matt! ${nyertes} nyert.`);
            } else {
                jatekVegeUzenet("Patt! Döntetlen.");
            }
        } else if (isKiralySakkban(kovetkezoJatekosSzin)) {
            const kiralyCella = document.getElementById(JSON.stringify(getKiralyPoz(kovetkezoJatekosSzin)));
            if (kiralyCella) kiralyCella.classList.add('sakkban-cella');
        }
    }

    function jatekVegeUzenet(uzenet) {
        jatekAktiv = false;
        const uzenetElem = document.getElementById('jatekVegeUzenet');
        if (uzenetElem) uzenetElem.innerText = uzenet;
        const overlayElem = document.getElementById('jatekVegeOverlay');
        if (overlayElem) overlayElem.style.display = 'flex';

        // TODO: Itt kéne hívni a saveMatch() PHP függvényt
        // Például egy AJAX hívással:
        // saveMatchToDatabase(nyertes, lepesLista);
    }

    // --- KATTINTÁS KEZELŐ FŐFÜGGVÉNY ---
    function kattintas(cella) {
        if (!jatekAktiv) return;
        removeHighlights();
        const poz = JSON.parse(cella.id);
        const celpontBabuInfo = getBabuAtPoz(poz);
        if (kijeloltBabu) {
            const ervenyesLepes = lehetsegesLepesek.find(lepes => JSON.stringify(lepes) === JSON.stringify(poz));
            if (ervenyesLepes) {
                lepesVegrehajtasa(kijeloltBabu, poz);
            } else {
                if (isSajatBabu(celpontBabuInfo)) {
                    kijeloltBabu = celpontBabuInfo;
                    lehetsegesLepesek = szabalyosLepesek(kijeloltBabu);
                    highlightLepesek(lehetsegesLepesek);
                    cella.classList.add('kijelolt-cella');
                } else {
                    kijeloltBabu = null;
                    lehetsegesLepesek = [];
                }
            }
        } else {
            if (isSajatBabu(celpontBabuInfo)) {
                kijeloltBabu = celpontBabuInfo;
                lehetsegesLepesek = szabalyosLepesek(kijeloltBabu);
                highlightLepesek(lehetsegesLepesek);
                cella.classList.add('kijelolt-cella');
            }
        }
    }

    // --- GYALOG BEÉRÉS ---
    function GyalogBeeres() {
        feherbabuk.forEach(b => {
            if (b.karakter === "P" && b.poz[0] === 8) {
                b.karakter = "KN"; b.kinezet = "♕";
            }
        });
        feketebabuk.forEach(b => {
            if (b.karakter === "p" && b.poz[0] === 1) {
                b.karakter = "kn"; b.kinezet = "♛";
            }
        });
    }

    // Biztosítjuk, hogy a render lefusson betöltéskor
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('sakktabla')) {
            init();
        }
    });
</script>
