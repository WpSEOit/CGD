# CalcCGD — Calcolatore dei benefici degli impianti fotovoltaici

**Descrizione breve**
Plugin WordPress per configurare e stimare benefici energetici/economici da impianti fotovoltaici in 4 step, con raccomandazione prodotto. Fonte: specifiche progetto interne.

## Funzionalità

* Configuratore multi-step (4 step) con Formidable Forms. Fonte: specifiche progetto interne.
* Calcoli server-side post-submit e salvataggio risultati nell’entry. Fonte: specifiche progetto interne.
* Output con proiezioni e suggerimento soluzione. Fonte: specifiche progetto interne.
* Shortcode pubblico: `[cgd-calculator]`. Fonte: specifiche progetto interne.

## Stato progetto

* Nome: **CalcCGD** | Slug: `calccgd` | Versione: `1` | Namespace: `CGD\Calc` | Text domain: `calccgd`. Fonte: specifiche progetto interne.

## Requisiti

* Versione minima WordPress: **dato non disponibile**
* Versione minima PHP: **dato non disponibile**
* Formidable Forms: **dato non disponibile**

## Installazione

1. Copiare la cartella del plugin in `wp-content/plugins/calccgd`.
2. Attivare il plugin da **Plugin > Installati**.
3. Assicurare la presenza di Formidable Forms attivo.
4. Inserire lo shortcode `[cgd-calculator]` nella pagina desiderata.
   Fonte: specifiche progetto interne.

## Configurazione rapida

1. Creare in Formidable Forms un form a 4 step con **field key** come sotto.
2. Annotare l’ID del form e impostarlo nel codice dove richiesto.
3. Pubblicare la pagina con lo shortcode.
   Fonte: specifiche progetto interne.

### Step e campi richiesti (field key)

**Step 1 – Consumi e spese**

* `gas_mc`, `gas_cost`
* `gpl_kg`, `gpl_cost`
* `biomass_ql`, `biomass_cost`
* `elec_kwh`, `elec_cost`
  Fonte: specifiche progetto interne.

**Step 2 – Fotovoltaico e sito**

* `roof_area_m2`
* `geo_zone` (`nord|centro|sud`)
* `roof_type` (`piano|falda`)
  Fonte: specifiche progetto interne.

**Step 3 – Riscaldamento**

* `heating_system` (`si|no`)
* `heating_terminals` (`si|no`)
* `remove_biomass` (`1|0`)
  Fonte: specifiche progetto interne.

**Step 4 – Edificio e status**

* `unit_type` (`appartamento_autonomo|villetta_schiera|casa_indipendente_4lati|condominio_centralizzato`)
* `roof_dispersion` (`1|0`)
* `roof_insulated` (`1|0`)
* `walls_insulated` (`1|0`)
* `area_m2`, `floors_n`, `windows_n`, `first_home` (`1|0`)
  Fonte: specifiche progetto interne.

## Output salvati (entry meta)

* `cgd_tot_spesa_annua`
* `cgd_tot_consumi_eq`
* `cgd_prod_fv_kwh`
* `cgd_risparmio_euro`
* `cgd_payback_anni`
* `cgd_suggerimento_codice`
  Definizioni formule e mapping: **dato non disponibile**. Fonte: specifiche progetto interne.

## Struttura progetto

```
calccgd/
├─ calccgd.php
├─ includes/
│  ├─ class-cgd-plugin.php
│  ├─ class-cgd-admin.php
│  └─ class-cgd-calculator.php
└─ uninstall.php
```

Fonte: specifiche progetto interne.

## Sicurezza e privacy

* Validazione, sanitizzazione e escaping in linea con WordPress. Riferimenti: **dato non disponibile in questo README**.
* Consenso privacy nel form e integrazione con esportazione/cancellazione dati: **dato non disponibile**.
  Fonte: specifiche progetto interne.

## Localizzazione

* Text domain: `calccgd`. Traduzioni: **dato non disponibile**. Fonte: specifiche progetto interne.

## Licenza

**dato non disponibile**.
