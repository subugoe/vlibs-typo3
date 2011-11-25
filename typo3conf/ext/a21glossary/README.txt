DEBUG CHEATSHEET
(GERMAN VERSION SEE BELOW)

Since the a21glossary is partially tricky to adjust, this part delivers useful behavioral feedback.
- You need to be logged into the Typo3-Backend, or the debug parameters wont work
- Typo3 Page Caching is disabled when using one of these options, so feel free to experiment.

How to use:
Simply add the params to your URL (POST is also supported if you want).
Alternately you can set then using typoscript (e.g. “config.tx_a21glossary.debug.info = 1”)


Very important:

	&tx_a21glossary[noncaching]=1	marks non-caching areas (if any), interesting to find potential bottlenecks
	-> red dashed for internal Typo3-Elements
	-> red dotted for external PHP-Scripts

	&tx_a21glossary[markers]=1		highlights the content being replaced by the glossary (green), highlights excluded parts/tags (red)

Important:

	&tx_a21glossary[highlight]=1	highlights the replaced glossar items in green for better recognition
	&tx_a21glossary[info]=1			shows some glossary stats below the content, eg. the runtime
	&tx_a21glossary[conf]=1			show glossary config below content
	&tx_a21glossary[trail]=1		who triggered the a21glossary (trail of function calls)

Less important:

	&tx_a21glossary[disable]=1		disables the glossary functionality
	&tx_a21glossary[query]=1		debug query to find glossary elements, one for each pid
	&tx_a21glossary[result]=1		debug all found glossary elements
	&tx_a21glossary[regexp]=1		show regular expression config below content
	&tx_a21glossary[keep]=1			keep markers in source after processing

Unimportant:

	&tx_a21glossary[input]=1		show raw input
	&tx_a21glossary[output]=1		show raw output
	&tx_a21glossary[demo]=1			add some demo-content and demo word ("etc.") into content




##############################
# in Deutsch
##############################

- Damit die Paramter anerkannt werden, muss man im Backend eingeloggt sein
- Auf der entsprechenden Frontend-Seite muss das Admin-Panel erscheinen, dort sicherheitshalber das Nicht-Cachen erzwingen
- dann die Parameter an die URL anfügen


Sehr wichtig:

	&tx_a21glossary[noncaching]=1	HEBT NICHT CACHENDE SEITENINHALTE HERVOR (SOFERN VORHANDEN)
	-> rot gestrichelt (dashed) for interne Typo3-Elemente
	-> rot Gepunktet (dotted) für externe PHP-Skripte

	&tx_a21glossary[markers]=1		Hervorhebung der aktiven Glossarbereiche in Grün, ausgeschlossene Bereiche in Rot

Wichtig:

	&tx_a21glossary[highlight]=1	Stärkere Hervorhebung der Glossarelemente (Neongrün)
	&tx_a21glossary[info]=1 		zeigt einige Statistiken (z.B. Laufzeit) an
	&tx_a21glossary[conf]=1			Zeigt die Glossarkonfiguration an (z.B. pids, excludeTags ...)
	&tx_a21glossary[trail]=1		Wer hat das Glossar getriggert (Trail der Funktionsaufrufe)

Weniger wichtig:

	&tx_a21glossary[disable]=1		deaktiviert das Glossar
	&tx_a21glossary[query]=1		SQL-Query, um Glossarelemente aufzufinden
	&tx_a21glossary[result]=1		SQL-Ergebnis der gefundenen Elemente
	&tx_a21glossary[regexp]=1		erstellte Regular Expressions
	&tx_a21glossary[keep]=1			keep markers in source after processing

Nicht Empfohlen:

	&tx_a21glossary[input]=1		content vor der verarbeitung
	&tx_a21glossary[output]=1		content nach der Verarbeitung
	&tx_a21glossary[demo]=1			Seiteninhalt mit Democonent und Glossar um Demobegriff ("etc.") erweitern

