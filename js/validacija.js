
document.addEventListener('DOMContentLoaded', function () {
    var telo = document.getElementById('telo-stavki');
    var dugmeDodaj = document.getElementById('dugme-dodaj-stavku');
    var forma = document.getElementById('forma-obracuna');
    var poljeOsnovna = document.getElementById('polje-osnovna');

    if (!telo || !forma) {
        return; // ova stranica nema formu obračuna
    }

    function napraviOpcije(izabranoId) {
        var html = '';
        VRSTE_STAVKI.forEach(function (v) {
            var izabrano = String(v.id) === String(izabranoId) ? 'selected' : '';
            html += '<option value="' + v.id + '" data-tip="' + v.tip + '" ' + izabrano + '>' +
                v.sifra + ' - ' + v.naziv + '</option>';
        });
        return html;
    }

    function osveziRedneBrojeve() {
        Array.prototype.forEach.call(telo.children, function (red, i) {
            red.querySelector('.redni-broj').textContent = i + 1;
        });
    }

    function osveziUkupno() {
        var uvecanje = 0;
        var umanjenje = 0;

        Array.prototype.forEach.call(telo.children, function (red) {
            var select = red.querySelector('select');
            var input = red.querySelector('input');
            var iznos = parseFloat((input.value || '0').replace(',', '.')) || 0;
            var tip = select.options[select.selectedIndex] ? select.options[select.selectedIndex].dataset.tip : '';

            if (tip === 'uvecanje') {
                uvecanje += iznos;
            } else if (tip === 'umanjenje') {
                umanjenje += iznos;
            }
        });

        var osnovna = parseFloat(((poljeOsnovna && poljeOsnovna.value) || '0').replace(',', '.')) || 0;

        document.getElementById('prikaz-uvecanje').textContent = uvecanje.toFixed(2);
        document.getElementById('prikaz-umanjenje').textContent = umanjenje.toFixed(2);
        document.getElementById('prikaz-neto').textContent = (osnovna + uvecanje - umanjenje).toFixed(2);
    }

    function dodajRed(vrstaId, iznos) {
        var red = document.createElement('tr');
        red.innerHTML =
            '<td class="redni-broj"></td>' +
            '<td><select name="stavka_vrsta[]" required>' + napraviOpcije(vrstaId) + '</select></td>' +
            '<td><input type="text" name="stavka_iznos[]" value="' + (iznos || '') + '" placeholder="0.00" required></td>' +
            '<td class="centrirano"><button type="button" class="dugme dugme-obris" title="Ukloni stavku">✕</button></td>';

        red.querySelector('.dugme-obris').addEventListener('click', function () {
            red.remove();
            osveziRedneBrojeve();
            osveziUkupno();
        });
        red.querySelector('select').addEventListener('change', osveziUkupno);
        red.querySelector('input').addEventListener('input', osveziUkupno);

        telo.appendChild(red);
        osveziRedneBrojeve();
    }

    dugmeDodaj.addEventListener('click', function () {
        dodajRed(null, '');
        osveziUkupno();
    });

    if (poljeOsnovna) {
        poljeOsnovna.addEventListener('input', osveziUkupno);
    }

    // Početno popunjavanje redova
    if (typeof POSTOJECE_STAVKE !== 'undefined' && POSTOJECE_STAVKE.length > 0) {
        POSTOJECE_STAVKE.forEach(function (s) {
            dodajRed(s.vrsta_id, s.iznos);
        });
    } else {
        dodajRed(null, '');
    }
    osveziUkupno();

    // ---------- Validacija pri slanju forme ----------
    forma.addEventListener('submit', function (e) {
        var ispravno = true;

        function proveriPolje(id, uslov, porukaGreske) {
            var polje = document.getElementById(id);
            if (!polje) {
                return;
            }
            var omot = polje.closest('.polje');
            var greska = omot.querySelector('.greska');
            if (!uslov) {
                omot.classList.add('nevalidno');
                if (greska) {
                    greska.textContent = porukaGreske;
                }
                ispravno = false;
            } else {
                omot.classList.remove('nevalidno');
            }
        }

        var ime = (document.getElementById('polje-ime').value || '').trim();
        var prezime = (document.getElementById('polje-prezime').value || '').trim();
        var jmbg = (document.getElementById('polje-jmbg').value || '').trim();
        var radnoMesto = (document.getElementById('polje-radno-mesto').value || '').trim();
        var brojSati = (document.getElementById('polje-broj-sati').value || '').trim();
        var osnovna = (poljeOsnovna.value || '').trim();
        var datumIsplate = (document.getElementById('polje-datum-isplate').value || '').trim();

        proveriPolje('polje-ime', ime !== '', 'Ime je obavezno.');
        proveriPolje('polje-prezime', prezime !== '', 'Prezime je obavezno.');
        proveriPolje('polje-jmbg', /^\d{13}$/.test(jmbg), 'JMBG mora imati tačno 13 cifara.');
        proveriPolje('polje-radno-mesto', radnoMesto !== '', 'Radno mesto je obavezno.');
        proveriPolje('polje-broj-sati', /^\d+$/.test(brojSati) && parseInt(brojSati, 10) > 0, 'Unesite ispravan broj radnih sati.');
        proveriPolje('polje-osnovna', parseFloat(osnovna.replace(',', '.')) > 0, 'Osnovna zarada mora biti veća od 0.');
        proveriPolje('polje-datum-isplate', datumIsplate !== '', 'Datum isplate je obavezan.');

        var poljeBroj = document.getElementById('polje-broj-obracuna');
        if (poljeBroj && !poljeBroj.readOnly) {
            proveriPolje('polje-broj-obracuna', poljeBroj.value.trim() !== '', 'Broj obračuna je obavezan.');
        }

        if (telo.children.length === 0) {
            alert('Obračun mora imati bar jednu stavku.');
            ispravno = false;
        } else {
            Array.prototype.forEach.call(telo.children, function (red) {
                var input = red.querySelector('input');
                var iznos = parseFloat((input.value || '0').replace(',', '.'));
                if (!iznos || iznos <= 0) {
                    input.style.borderColor = '#dc2626';
                    ispravno = false;
                } else {
                    input.style.borderColor = '';
                }
            });
        }

        if (!ispravno) {
            e.preventDefault();
        }
    });
});
