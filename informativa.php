<!--
  Bozza di Informativa Privacy GDPR - versione HTML
  ATTENZIONE: è una bozza generica. Personalizzala con i dati reali (titolare, PEC/email, tempi di conservazione, hosting, ecc.)
  Verifica finale consigliata con consulente legale/DPO.
-->

<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Informativa Privacy - [NomeSito]</title>
    <style>
        /* Stili minimi per leggibilità — modifica liberamente */
        body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; line-height:1.6; color:#111; padding:24px; background:#f9fafb; }
        .container { max-width:900px; margin:0 auto; background:#fff; padding:28px; border-radius:8px; box-shadow:0 6px 18px rgba(15,15,15,0.06); }
        h1,h2,h3 { color:#0b3b66; margin-top:1.2em; }
        p { margin: .6em 0; }
        ul { margin: .6em 0 1.2em 1.4em; }
        .note { background:#fff7cc; padding:12px; border-left:4px solid #ffd24d; border-radius:4px; }
        .muted { color:#666; font-size:.95em; }
        .small { font-size:.9em; color:#555; }
        a { color:#0b66a6; text-decoration:underline; }
        .btn { display:inline-block; padding:10px 14px; background:#0b66a6; color:white; border-radius:6px; text-decoration:none; margin-top:12px; }
        footer { margin-top:20px; font-size:.9em; color:#666; }
    </style>
</head>
<body>
<div class="container" role="main" aria-labelledby="title">
    <h1 id="title">Informativa privacy ai sensi del Regolamento (UE) 2016/679 (GDPR)</h1>

    <p class="muted"><strong>Data di revisione:</strong> <span id="rev-date">[inserire data]</span></p>

    <section aria-labelledby="titolare">
        <h2 id="titolare">Titolare del trattamento</h2>
        <p>
            Il titolare del trattamento è <strong>[Nome e Cognome]</strong>, persona fisica, residente a
            <strong>[Città/Provincia]</strong>
        </p>
        <p>Per informazioni e richieste relative al trattamento possono essere contattati:</p>
        <ul>
            <li>Email: <a href="mailto:[inserire-email]">[inserire-email]</a></li>
            <li>PEC: <a href="mailto:[inserire-pec]">[inserire-pec]</a> (se disponibile)</li>
            <li>Telefono: <strong>[inserire numero]</strong></li>
        </ul>
    </section>

    <section aria-labelledby="dati-raccolti">
        <h2 id="dati-raccolti">1. Tipologie di dati raccolti</h2>
        <p>
            Tramite il modulo di registrazione raccogliamo le seguenti categorie di dati personali:
        </p>
        <ul>
            <li>Nome e cognome</li>
            <li>Indirizzo e-mail</li>
            <li>Numero di telefono cellulare</li>
            <li>Password (conservata in forma protetta/hashata)</li>
        </ul>
        <p class="small">Non trattiamo (salvo diversa indicazione) “categorie particolari” di dati sensibili ai sensi dell'art. 9 GDPR.</p>
    </section>

    <section aria-labelledby="finalita">
        <h2 id="finalita">2. Finalità e base giuridica del trattamento</h2>
        <p>I dati sono trattati per le seguenti finalità:</p>
        <ul>
            <li><strong>Creazione e gestione dell’account</strong> (base giuridica: esecuzione di un contratto o misure precontrattuali — art. 6(1)(b) GDPR).</li>
            <li><strong>Comunicazioni di servizio</strong> inerenti l’account (es. reset password, notifiche tecniche).</li>
            <li><strong>Adempimenti legali</strong> (es. obblighi fiscali o di sicurezza) — art. 6(1)(c) GDPR.</li>
            <li><strong>Invio di comunicazioni promozionali/newsletter</strong> — solo previo consenso esplicito, fornito separatamente dall’accettazione del servizio (art. 6(1)(a) GDPR).</li>
        </ul>
    </section>

    <section aria-labelledby="modalita">
        <h2 id="modalita">3. Modalità del trattamento e misure di sicurezza</h2>
        <p>
            Il trattamento viene effettuato con strumenti elettronici e informatizzati e con misure tecniche e organizzative adeguate a garantire la riservatezza, integrità e disponibilità dei dati.
        </p>
        <ul>
            <li>Connessioni protette tramite HTTPS/TLS;</li>
            <li>Password conservate con algoritmi di hashing sicuro (es. Argon2id / bcrypt) e salt univoci;</li>
            <li>Accesso ai dati limitato al personale autorizzato;</li>
            <li>Rate limiting e misure anti-brute force per i tentativi di accesso;</li>
            <li>Log di sicurezza gestiti con attenzione a non includere dati sensibili in chiaro.</li>
        </ul>
    </section>

    <section aria-labelledby="conservazione">
        <h2 id="conservazione">4. Periodo di conservazione</h2>
        <p>
            I dati sono conservati per il tempo necessario alle finalità per cui sono stati raccolti.
            In particolare:
        </p>
        <ul>
            <li>Dati account attivi: conservazione fino alla cancellazione dell’account da parte dell’utente o fino alla cessazione del servizio;</li>
            <li>Account inattivi: i profili inattivi da oltre <strong>48 mesi</strong> potranno essere cancellati o anonimizzati;</li>
            <li>Dati conservati per obblighi di legge: nei termini previsti dalle norme applicabili.</li>
        </ul>
        <p class="small">Definire e documentare le politiche di retention nel registro delle attività del trattamento (RoPA).</p>
    </section>

    <section aria-labelledby="destinatari">
        <h2 id="destinatari">5. Destinatari dei dati</h2>
        <p>I dati potranno essere comunicati a:</p>
        <ul>
            <li>Fornitori di servizi tecnici e di hosting (es. provider server, servizi cloud);</li>
            <li>Fornitori di strumenti per l'invio di email (solo se previsto);</li>
            <li>Personale interno autorizzato per attività di gestione e supporto;</li>
            <li>Autorità competenti, qualora la comunicazione sia imposta da obblighi di legge.</li>
        </ul>
        <p>
            Nel caso di trasferimenti verso Paesi extra-UE saranno adottate adeguate garanzie (es. Clausole Contrattuali Standard o decisione di adeguatezza).
        </p>
    </section>

    <section aria-labelledby="diritti">
        <h2 id="diritti">6. Diritti dell’interessato</h2>
        <p>
            L’interessato ha il diritto di esercitare i diritti previsti dagli artt. 15-22 GDPR:
        </p>
        <ul>
            <li>Diritto di accesso (art.15);</li>
            <li>Diritto di rettifica (art.16);</li>
            <li>Diritto alla cancellazione - "diritto all'oblio" (art.17);</li>
            <li>Diritto di limitazione del trattamento (art.18);</li>
            <li>Diritto alla portabilità dei dati (art.20);</li>
            <li>Diritto di opposizione (art.21);</li>
            <li>Diritto di revocare il consenso senza pregiudicare la liceità del trattamento basata sul consenso antecedente alla revoca (art.7).</li>
        </ul>
        <p>
            Per esercitare i diritti rivolgersi a: <a href="mailto:[inserire-email]">[inserire-email]</a>.
        </p>
    </section>

    <section aria-labelledby="reclamo">
        <h2 id="reclamo">7. Reclamo all’Autorità di controllo</h2>
        <p>
            Se ritieni che il trattamento dei tuoi dati avvenga in violazione del GDPR, puoi proporre reclamo al Garante per la protezione dei dati personali (www.garanteprivacy.it).
        </p>
    </section>

    <section aria-labelledby="aggiornamenti">
        <h2 id="aggiornamenti">8. Aggiornamenti dell'informativa</h2>
        <p>
            La presente informativa potrà essere aggiornata. Eventuali modifiche saranno pubblicate su questa pagina con la relativa data di revisione.
        </p>
    </section>

    <div class="note" role="note">
        <strong>Nota:</strong> questa è una bozza standard. Prima della pubblicazione finale:
        <ul>
            <li>Inserisci i dati effettivi del titolare, contatti e tempi di conservazione;</li>
            <li>Verifica i servizi esterni (hosting, email provider) e aggiungi eventuali trasferimenti extra-UE;</li>
            <li>Se prevedi marketing via email/SMS aggiungi la sezione sul consenso specifico e il link di opt-out;</li>
            <li>Consulenza legale/DPO consigliata per compliance completa.</li>
        </ul>
    </div>

    <hr/>

</div>
</body>
</html>
