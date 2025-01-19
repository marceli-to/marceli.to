<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Statamic\Entries\Entry;
use Statamic\Taxonomies\Term;

class ImportProjects extends Command
{

  protected $projects = [
    [
      'uri' => 'https://visualisierungs-akademie.ch/',
      'website' => 'visualisierungs-akademie.ch',
      'description' => 'Webseite und Buchungsplattform für die Visualisierungs Akademie in Zürich.',
      'image' => 'visualisierungs-akademie.ch',
      'client' => 'Visualisierungs-Akademie Schweiz GmbH, Zürich',
      'design' => 'WBG AG – Visuelle Kommunikation, Zürich',
      'year' => '2023'
    ],
    [
      'uri' => 'https://drive-academy.ch/',
      'website' => 'drive-academy.ch',
      'description' => 'Webseite für die PS Drive Academy',
      'image' => 'drive-academy.ch',
      'client' => 'PS Drive Academy, Hinwil',
      'design' => 'stoz Werbeagentur',
      'year' => '2023'
    ],
    [
      'uri' => 'https://ruem.ch/',
      'website' => 'ruem.ch',
      'description' => 'Webseite für die Innenarchitektin Jenny Schäubli',
      'image' => 'ruem.ch',
      'client' => 'Jenny Schäubli, Zürich',
      'design' => 'Emma Leuthold',
      'year' => '2023'
    ],
    [
      'uri' => 'https://gmuerarch.ch/',
      'website' => 'gmuerarch.ch',
      'description' => 'Webseite für Silvia Gmür Reto Gmür Architekten in Basel',
      'image' => 'gmuerarch.ch',
      'client' => 'ilvia Gmür Reto Gmür Architekten, Basel',
      'design' => 'Hans Grüeninger',
      'year' => '2023'
    ],
    [
      'uri' => 'https://zuerigaertner.ch/',
      'website' => 'zuerigaertner.ch',
      'description' => 'Webseite für ZÜRIGÄRTNER GmbH',
      'image' => 'zuerigaertner.ch',
      'client' => 'ZÜRIGÄRTNER GmbH',
      'design' => 'stoz Werbeagentur',
      'year' => '2023'
    ],
    [
      'uri' => 'https://steckdosen-kombination.ch/',
      'website' => 'steckdosen-kombination.ch',
      'description' => 'Konfigurator für Wand-Stromverteiler',
      'image' => 'steckdosen-kombination',
      'client' => 'Ferratec AG, Rudolfstetten',
      'design' => 'Vadim Zvyagintsev',
      'year' => '2022'
    ],
    [
      'uri' => 'https://forum-architektur.ch/',
      'website' => 'forum-architektur.ch',
      'description' => 'Webseite für das Forum Architektur Winterthur',
      'image' => 'forum-architektur-winterthur',
      'client' => 'Forum Architektur, Winterthur',
      'design' => 'Bivgrafik GmbH, Zürich',
      'year' => '2022'
    ],
    [
      'uri' => 'https://itavolidelvagabondo.ch/',
      'website' => 'itavolidelvagabondo.ch',
      'description' => 'Webseite für Simon Schneeberger a.k.a. I tavoli del vagabondo. Buon appetito.',
      'image' => 'itavolidelvagabondo.ch',
      'client' => 'Associazione I tavoli del Vagabondo, Winterthur',
      'design' => 'Pool Practice, Berlin',
      'year' => '2022'
    ],
    [
      'uri' => 'https://20years.quality1.ch/',
      'website' => '20years.quality1.ch',
      'description' => 'Landingpage für das 20 Jahre Jubiläum der Quality1 AG.',
      'image' => '20years.quality1.ch',
      'client' => 'Quality1 AG, Bubikon',
      'design' => 'Stefanie Wolf',
      'year' => '2022'
    ],
    [
      'uri' => 'https://herkulesdesign.com/',
      'website' => 'herkulesdesign.com',
      'description' => 'Portfolio Webseite für Herkules Design GmbH.',
      'image' => 'herkulesdesign.com',
      'client' => 'Herkules Design GmbH, Winterthur',
      'design' => 'Herkules Design GmbH, Winterthur',
      'year' => '2021'
    ],
    [
      'uri' => 'https://danielh.ch',
      'website' => 'danielh.ch',
      'description' => 'Der Klassiker vom Chreis Cheib. Das Lokal lädt alle ein, die den Mix aus Feinschmecker- und Szenenkultur lieben, die gerne parlieren, dinieren und sich amüsieren.',
      'image' => 'danielh.ch',
      'client' => 'Daniel H., Zürich',
      'design' => "Jam'on digital",
      'year' => '2021'
    ],
    [
      'uri' => 'https://gutebauten.stadt-zuerich.ch/',
      'website' => 'gutebauten.stadt-zuerich.ch',
      'description' => 'Publikums-Votingseite zur Auszeichnung für gute Bauten der Stadt Zürich 2021.',
      'image' => 'gute-bauten-stadt-zuerich',
      'client' => 'Stadt Zürich Hochbaudepartement',
      'design' => 'Bivgrafik GmbH, Zürich',
      'year' => '2021'
    ],
    [
      'uri' => 'https://mobilecharge.ch/',
      'website' => 'mobilecharge.ch',
      'description' => 'Landingpage und Konfigurator für eMobility Ladestationen.',
      'image' => 'mobilecharge.ch',
      'client' => 'Ferratec AG, Rudolfstetten',
      'design' => 'Vadim Zvyagintsev',
      'year' => '2021'
    ],
    [
      'uri' => 'https://www.lifework.ch/',
      'website' => 'lifework.ch',
      'description' => 'Corporate Webseite für lifework tk ag in Winterthur',
      'image' => 'lifework.ch',
      'client' => 'lifework tk ag, Winterthur',
      'design' => 'Alexandra Noth, Winterthur',
      'year' => '2021'
    ],
    [
      'uri' => 'https://www.riccardacavelti.ch/',
      'website' => 'riccardacavelti.ch',
      'description' => 'Corporate Webseite für die Immobilienexpertin Riccarda Cavelti.',
      'image' => 'riccardacavelti',
      'client' => 'Riccarda Cavelti GmbH, Zürich',
      'design' => 'WBG AG – Visuelle Kommunikation, Zürich',
      'year' => '2021'
    ],
    [
      'uri' => 'https://hausermeier.ch',
      'website' => 'hausermeier.ch',
      'description' => 'Portfolio Webseite für das Architektinnen-Duo Martina Hauser und Sabine Meier.',
      'image' => 'hausermeier',
      'client' => 'Hauser Meier Architektinnen, Zürich',
      'design' => 'Bivgrafik GmbH, Zürich',
      'year' => '2020'
    ],
    [
      'uri' => 'https://day.ch',
      'website' => 'day.ch',
      'description' => 'Corporate website for the shoe manufacturer Day in Zurich. Online store is coming soon.',
      'image' => 'day',
      'client' => 'Day AG, Zürich',
      'design' => 'WBG AG – Visuelle Kommunikation, Zürich',
      'year' => '2020'
    ],
    [
      'uri' => 'https://sipt.ch',
      'website' => 'sipt.ch',
      'description' => 'Webseite und Kursverwaltung für Studenten und Dozenten für das Schweizer Institut für Psychotraumatologie in Winterthur.',
      'image' => 'sipt',
      'client' => 'Schweizer Institut für Psychotraumatologie, Winterthur',
      'design' => 'Alexandra Noth, Winterthur',
      'year' => '2020'
    ],
    [
      'uri' => 'https://aas.aero',
      'website' => 'aas.aero',
      'description' => 'Landingpage für Airline Assistance Switzerland.',
      'image' => 'aas-aero',
      'client' => 'AAS, Switzerland',
      'design' => 'WBG AG – Visuelle Kommunikation, Zürich',
      'year' => '2020'
    ],
    [
      'uri' => 'https://sajo.ch',
      'website' => 'sajo.ch',
      'description' => 'Online-Shop und Portfolio des Illustrators und Künstlers Samuel Jordi.',
      'image' => 'sajo',
      'client' => 'Samuel Jordi, Winterthur',
      'design' => 'Samuel Jordi, Winterthur',
      'year' => '2020'
    ],
    [
      'uri' => 'https://oxid-architektur.ch',
      'website' => 'oxid-architektur.ch',
      'description' => 'Portfolio Webseite für das Zürcher Architekturbüro Oxid.',
      'image' => 'oxid-architektur',
      'client' => 'Oxid Architektur GmbH, Zürich',
      'design' => 'Bivgrafik GmbH, Zürich',
      'year' => '2020'
    ],
    [
      'uri' => 'https://mobilecharge.ch',
      'website' => 'mobilecharge.ch',
      'description' => 'Konfigurator für eMobility Ladestationen.',
      'image' => 'mobilecharge',
      'client' => 'Ferratec AG, Rudolfstetten',
      'design' => "Jam'on digital AG, Zürich",
      'year' => '2020'
    ],
    [
      'uri' => 'https://intercable-schweiz.ch',
      'website' => 'intercable-schweiz.ch',
      'description' => 'How to correctly use tools from Intercable.',
      'image' => 'intercable-schweiz',
      'client' => 'Ferratec AG, Rudolfstetten',
      'design' => 'marceli.to, Winterthur',
      'year' => '2020'
    ],
    [
      'uri' => 'https://nightnurse.ch',
      'website' => 'nightnurse.ch',
      'description' => 'Visualisierungen. 3D-Animationen. VR-Erlebnisse. Und vieles mehr.. Nightnurse Images AG, Zürich',
      'image' => 'nightnurse',
      'client' => 'Nightnurse Images AG, Zürich',
      'design' => 'WBG AG – Visuelle Kommunikation, Zürich',
      'year' => '2016 – 2020'
    ],
    [
      'uri' => 'https://rebwein.ch',
      'website' => 'rebwein.ch',
      'description' => 'Webseite und Shop für REB Wein AG, Zürich.',
      'image' => 'rebwein',
      'client' => 'Rebwein AG, Zürich',
      'design' => "Jam'on digital AG, Zürich",
      'year' => '2019'
    ],
    [
      'uri' => 'https://wbg.ch',
      'website' => 'wbg.ch',
      'description' => 'Portfolio Webseite für WBG, Zürich.',
      'image' => 'wbg',
      'client' => 'WBG AG – Visuelle Kommunikation, Zürich',
      'design' => 'WBG AG – Visuelle Kommunikation, Zürich',
      'year' => '2019'
    ],
    [
      'uri' => 'https://strut.ch',
      'website' => 'strut.ch',
      'description' => 'Portfolio Webseite für das Winterthurer Architekturbüro Strut Architekten.',
      'image' => 'strut',
      'client' => 'Strut Architekten AG, Winterthur',
      'design' => 'Bivgrafik GmbH, Zürich',
      'year' => '2019'
    ],
    [
      'uri' => 'https://studioammeer.ch',
      'website' => 'studioammeer.ch',
      'description' => 'Gesamtauftritte. Gedrucktes. Digitales. Studio am Meer, Winterthur',
      'image' => 'studio-am-meer',
      'client' => 'Studio am Meer, Winterthur',
      'design' => 'Studio am Meer, Winterthur',
      'year' => '2018'
    ]
  ];

  protected $signature = 'import:projects';

  protected $description = 'Import projects into statamic projects collection';


  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    

    foreach ($this->projects as $project)
    {

      // Get Taxonomy term for "client"
      $client = \Statamic\Facades\Taxonomy::findByHandle('clients')
        ->queryTerms()
        ->where('title', $project['client'])
        ->first();

      // Create client if not exists
      if (!$client) {
        $client = \Statamic\Facades\Term::make()
          ->taxonomy('clients')  // use taxonomy() instead of collection()
          ->slug(\Str::slug($project['client']))  // slug is required
          ->data(['title' => $project['client']])
          ->save();
      }

      // Get Taxonomy term for "agency"
      $agency = \Statamic\Facades\Taxonomy::findByHandle('clients')
        ->queryTerms()
        ->where('title', $project['design'])
        ->first();

      // Create client if not exists
      if (!$agency) {
          $agency = \Statamic\Facades\Term::make()
          ->taxonomy('clients')  // use taxonomy() instead of collection()
          ->slug(\Str::slug($project['design']))  // slug is required
          ->data(['title' => $project['design']])
          ->save();
      }

      Entry::make()
        ->collection('projects')
        ->data([
          'title' => $project['website'],
          'slug' => \Str::slug($project['website']),
          'description' => $project['description'],
          'link' => $project['uri'],
          'year' => $project['year'],
          'image' => "projects/" . $project['image'] . '.jpg',
          'client' => $client->slug ?? null,
          'agency' => $agency->slug ?? null,
        ])
        ->save();

      $this->info('Imported: ' . $project['website']);
    }

    return 0;
  }

}