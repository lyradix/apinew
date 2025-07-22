/*Table poi*/
INSERT INTO poi (id, type, properties, geometry) 
VALUES ( 0, 
    'Feature', 
    '{
        "popup": "Entrée/sortie du festival",
        "type": "start",
        "marker-color": "#4b9baf",
        "marker-size": "medium",
        "marker-symbol": "gate",
        "image": "snack"
    }',
    ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19484090805053, 48.8304724870417]}')
),
		(1, 'Feature',  '{
        "popup": "Entré scènes",
        "type": "entry",
        "marker-color": "#4b9baf",
        "marker-size": "medium",
        "marker-symbol": "entry",
        "image":"gate"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19503402709961,48.833565844027]}')),
    (2, 'Feature',  '{
        "popup": "Point restauration",
        "type": "snack",
        "marker-color": "#e1870f",
        "marker-size": "medium",
        "marker-symbol": "restaurant",
        "image":"snack"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19642877578735,
		48.8341308201092]}')), 
     (3, 'Feature',  '{
        "marker-color": "#ff870f",
        "marker-size": "medium",
        "marker-symbol": "restaurant",
        "type": "snack",
        "popup": "Point restauration",
        "image":"snack"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.192414634227049,
		48.83544600108655]}')),
        (4, 'Feature',  '{
        "popup": "Point restauration",
        "type": "snack",
        "marker-color": "#ff870f",
        "marker-size": "medium",
        "marker-symbol": "restaurant",
        "image":"snack"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19685792922973,
		48.83615055749969]}')),
     (5, 'Feature',  '{
        "popup": "Point restauration C",
        "type": "snack",
        "marker-color": "#ff870f",
        "marker-size": "medium",
        "marker-symbol": "restaurant",
        "image":"snack"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19404697418212,
		48.8331138585738]}')),
	     (6, 'Feature',  '{
        "popup": "eXageras",
        "type": "scène",
        "marker-color": "#d21e96",
        "marker-size": "medium",
        "marker-symbol": "theatre",
        "image":"star"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19488382339477,
		48.8356420998122]}')),
         (7, 'Feature',  '{
        "popup": "eXageras",
        "type": "scène",
        "marker-color": "#d21e96",
        "marker-size": "medium",
        "marker-symbol": "theatre",
        "image":"star"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19194412231445,
		48.8356562237065]}')),  
    (8, 'Feature',  '{
       "popup": "eXodius",
        "type": "scène",
        "marker-color": "#d21e96",
        "marker-size": "medium",
        "marker-symbol": "theatre",
        "image":"star"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19866037368774,
		48.836094062456]}')), 
     (9, 'Feature',  '{
      "popup": "eXperienza",
        "type": "scène",
        "marker-color": "#d21e96",
        "marker-size": "medium",
        "marker-symbol": "theatre",
        "image":"star"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19878911972045,
		48.8348935277137]}')),  
      (10, 'Feature',  '{
        "popup": "eXtasia",
        "type": "scène",
        "marker-color": "#d21e96",
        "marker-size": "medium",
        "marker-symbol": "theatre",
        "image":"star"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19400405883789,
          48.8343568087582]}')),
        (11, 'Feature',  '{
     "popup": "Toilettes",
        "type": "wc",
        "marker-color": "#7e7e7e",
        "marker-size": "medium",
        "marker-symbol": "toilet",
        "image":"toilet"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19754457473754,
		48.8343850572676]}')),   
       (12, 'Feature',  '{
       "popup": "Toilettes",
        "type": "wc",
        "marker-color": "#7e7e7e",
        "marker-size": "medium",
        "marker-symbol": "toilet",
        "image":"toilet"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19891786575317,
		48.8353878690331]}')),
       (13, 'Feature',  '{
        "popup": "Toilettes",
        "type": "wc",
        "marker-color": "#7e7e7e",
        "marker-size": "medium",
        "marker-symbol": "toilet",
        "image":"toilet"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19891786575317,
          48.8353878690331]}')),   
       (14, 'Feature',  '{
          "popup": "Toilettes",
        "type": "wc",
        "marker-color": "#7e7e7e",
        "marker-size": "medium",
        "marker-symbol": "toilet",
        "image":"toilet"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19419717788696,
          48.8300063481985]}')),      
      (15, 'Feature',  '{
         "popup": "Toilettes",
        "type": "wc",
        "marker-color": "#7e7e7e",
        "marker-size": "medium",
        "marker-symbol": "toilet",
        "image":"toilet"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19029188156127,
          48.8333257272627]}')), 
   (16, 'Feature',  '{
        "popup": "Toilettes",
        "type": "wc",
        "marker-color": "#7e7e7e",
        "marker-size": "medium",
        "marker-symbol": "toilet",
        "image":"toilet"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19265222549438,
          48.8333116027113]}')), 
    (17, 'Feature',  '{
        "popup": "camping A",
        "type": "camping",
        "marker-color": "#7d5a28",
        "marker-size": "medium",
        "marker-symbol": "campsite",
        "image":"camping"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19099998474121,
          48.8334810970651]}')),     
     (18, 'Feature',  '{
        "popup": "camping B",
        "type": "camping",
        "marker-color": "#7d5a28",
        "marker-size": "medium",
        "marker-symbol": "campsite",
        "image":"camping"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19205141067504,
          48.8328454902816]}')),           
    (19, 'Feature',  '{
        "popup": "camping C",
        "type": "camping",
        "marker-color": "#7d5a28",
        "marker-size": "medium",
        "marker-symbol": "campsite",
        "image":"camping"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.1918,
          48.8336]}')), 
     (20, 'Feature',  '{
        "popup": "camping D",
        "type": "camping",
        "marker-color": "#7d5a28",
        "marker-size": "medium",
        "marker-symbol": "campsite",
        "image":"camping"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.1911072731018,
          48.8328313655949]}')), 
       (21, 'Feature',  '{
        "popup": "coin médical",
        "type": "medical",
        "marker-color": "#ff0000",
        "marker-size": "medium",
        "marker-symbol": "hospital",
        "image":"hospital"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19630002975463,
          48.8336788397531]}')),    
          (22, 'Feature',  '{
        "popup": "coin médical",
        "type": "medical",
        "marker-color": "#ff0000",
        "marker-size": "medium",
        "marker-symbol": "hospital",
        "image":"hospital"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19522714614868,
          48.830924496324]}')),           
       (23, 'Feature',  '{
            "popup": "coin médical",
        "type": "medical",
        "marker-color": "#ff0000",
        "marker-size": "medium",
        "marker-symbol": "hospital",
        "image":"hospital"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19522714614868,
          48.830924496324]}')),        
	(24, 'Feature',  '{
		"popup": "Exit",
        "type": "exit",
        "marker-color": "#2d642d",
        "marker-size": "medium",
        "marker-symbol": "arrow",
        "image":"exit"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [2.19904661178588,
          48.8360375673485]}')),
	(25, 'Feature',  '{
		"popup": "Exit",
        "marker-color": "#2d642d",
        "marker-size": "medium",
        "marker-symbol": "arrow",
        "image":"exit"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [ 2.218435129682746,
            48.83965677595668]}')),
   (26, 'Feature',  '{
		 "popup": "Parking",
          "type": "parking"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [ 2.218435129682746,
            48.83965677595668]}')),   
	   (27, 'Feature',  '{
		 "popup": "Parking",
          "type": "parking"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [ 2.212591899934125,
            48.83668103771694]}')),  	
         (28, 'Feature',  '{
		 "popup": "Parking",
          "type": "parking"
      }',
	ST_GeomFromGeoJSON('{"type": "Point", "coordinates": [ 2.2226053047783125,
            48.83461075983277]}'));
	SELECT * 
    FROM poi;

/*scene table*/
INSERT INTO scene
		(id,nom,poi_fk_id)
		VALUES
        (1,'eXodius',8),
		(2,'eXageras',7),
		(3,'eXperienza',9),
		(4,'eXtravaganza !',6),
		(5,'eXtasia',10);

SELECT *                 
FROM scene;

/*Table partners */
INSERT INTO partners
			(title, front_page, type, link,partner_id)
			VALUES
			('Affligen',true,'Restauration',
			'https://www.affligembeer.com/fr/fr/age-gate?returnUrl=https%3A%2F%2Fwww.affligembeer.com%2Ffr%2Ffr%2Fhomepage',
			'a1'),
			('Breiz Cola',true,'Restauration',
			'https://www.breizhcola.bzh/',
			'a2'),
			('Mcdonalds',true,'Restauration',
			'https://www.mcdonalds.fr/?gclsrc=aw.ds&gad_source=1&gclid=CjwKCAiA3ZC6BhBaEiwAeqfvyrLYv4OaWzd6rcLBBhiVLHp7mlZU1gSKPIpc7y0z26IKd3a9A4PrNBoCe13QAvD_BwE&gclsrc=aw.ds',
			'a3'),
			('Cristaline',false,'Restauration',
			'https://www.moneaucristaline.fr/origine-et-sources/',
			'a4'),
			('Dominos',true,'Restauration',
			'https://www.dominos.fr/?gad_source=1&gclid=CjwKCAiA3ZC6BhBaEiwAeqfvypMnQ9Po4ArPsVzXvJjlGvu2uDeljhUJVpZmbbf6xX98QfkE0Ox3HhoCmlAQAvD_BwE',
			'a5'),
			('Crédit Agricole',true,'Sponsor',
			'https://www.credit-agricole.com/',
			'b1'),
			('Citroen',false,'Sponsor',
			'https://www.citroen.fr/',
			'b2'),
			('Engie',true,'Sponsor',
			'https://www.engie.com/jobs/alternance',
			'b3'),
			('NRJ',true,'Media',
			'https://www.nrj.fr/',
			'c1'),
			('Deezer',true,'Media',
			'https://www.deezer.com/fr/offers/premium/acq?utm_source=adwords&utm_medium=search&utm_campaign=acq_fr_sea-brd_web_search_perf_tnb-directsub_pure&utm_content=brd_premium_tnb_pure-exact&utm_term=deezer&gad_source=1&gclid=CjwKCAiA3ZC6BhBaEiwAeqfvyin6TjPo4hPeVWy_ORcm9K14Wl0pfoD_NFi0yZaGoZZ9_CmKtIS5nhoCro0QAvD_BwE',
			'c2'),
			('ladepeche',false,'Media',
			'https://www.ladepeche.fr/',
			'c3');

SELECT *
FROM partners; 

/*table info*/
INSERT INTO info
			(title, type, descriptif)
			VALUES
			('Le déroulement', 'déroulement', 
			'Le festival se tientdra sur 3 jours à Domaine national de St Cloud, dans la région Parisienne'
			),
			('En train SNCF', 'Venir',
			 'La SNCF a mis à disposition de bon plans pour les :\nLes moins de 26 ans\nLes tarrifs spéciaux\nPour plus d''infos, rendez vous sur le site du SNCF'
			),
			('En voiture','Venir', 
			'Vous aurez des places de parking prés du site:\nParking de l''allée du Diable - 7 mins à pieds du domaines\nParking Indigo Saint-Cloud Orléans - 12 mins à pieds du domaines\nParking Indigo Sèvres île de Monsieur - 11 mins à pieds du domaines\nVous avez la posssibilité de venir en co-voiturage aussi.'
			),
			('Horaires', 'surPLace',
            'Le park ouvre à partir de 11h30\nLes concerts commencent à partir de 13h\nLes restaurants sont ouverts 24h/24h\n'
			),
			('restauration', 'surPLace',
			'Au menu:\nBurgers\nPizzas\nFrites\nPaninis\nKebab\nPlat végétarien\n'
			),
			('La sécurité', 'surPLace',
			'Avec ceci:\nCoca\nVin chaud'
			),
			('Plan', 'surPLace',
			' '
			),
			('Objets Interdits', 'Interdits',
            'Engins pyrotechniques et fumigènes\nOutils de bricolage\nBoissons et récipients\nArmes de toutes catégories'
			),
			('Accéssibilité', 'handi',
			'Accès à un parking et un camping réservés proches du site - Plateformes avec vue sur les scenes principales - WC et douches adaptés, bornes de recharge pour fauteuils roulants, boucles magnétiques, etc.'
			),
			('Revente de billets', 'revente',
			'La revente à un prix supérieur est strictement interdite et punie par la loi (jusqu''à 30 000 € d’amende en cas de récidive). Merci de votre vigilance !'
			),
			('Qui nous sommes', 'mainInfo',
			'Live Event est une société présent dans l''évènementiel depuis  qulques années déjà. Nous organisons des tournées en France ainsi qu''en Europe. Nous faisons également la promotion de concerts d''artistes connus et moins connus du public. Nous avons le plaisirs de manager et produire de beaux talents'
			);

SELECT *
FROM info;


/*Table artist*/
INSERT INTO artist
		(id,nom,start_time , end_time, famous_song, genre, description, source, lien, scene_fk_id, image)
		VALUES
		(1,'RVG', '2025-09-12 14:20:00', '2025-09-12 17:40:00', 'Nothing Really Changes', 'Rock',
		'Leur musique est référencée, classique mais pas passéiste. Cette formation de Melbourne est notre coup de cœur du festival Les femmes s’en mêlent, à voir au Café',
		'Télérama',
		'https://www.telerama.fr/musique/rvg-le-groupe-de-rock-sauvage-et-sophistique-qui-nous-fait-chavirer-7018312.php#:~:text=RVG,%20le%20groupe%20de%20rock%20sauvage%20et%20sophistiqu%C3%A9%20qui%20nous '
		,1, "image-1.jpg"),
		(2,'Samba de la Muerte', '2025-09-12 17:00:00','2025-09-12 18:20:00','You''ll Never Know When I Lie ','Electro-organique',
		'Samba de la Muerte est un quatuor normand cherchant à brouiller les pistes et à effacer les frontières musicales à coup de puissantes mélodies électroniques. La preuve est dans le nom du groupe : essayez donc de danser la samba sur leur musique ! Projet créé à l’initiative d’Adrien Lepêtre en 2012, le groupe se positionne rapidement comme un créateur d’ambiance unique et une figure incontournable dans la  électronique, grâce à des boucles d’électro organiques mélangées à des influences provenant principalement d’Orient et d’Afrique de l’Ouest.',
		'artistikrezo.com',
		'https://www.artistikrezo.com/musique/samba-de-la-muerte-focus-sur-un-groupe-normand-hors-du-commun.html#:~:text=Samba%20de%20la%20Muerte%20est%20un%20quatuor%20normand%20cherchant%20%C3%A0 '
		,2, "image-2.jpg"),
		(3,'Sœurs Malsaines', '2025-09-12 19:05:00','2025-09-12 20:05:00', 'Hathor', 'Electro', 
        'Conscience des minorités, check tes privilèges La remise en question de ses propres privilèges, de sa place dans la société. Une prise de conscience collective. Partons du principe que nous reproduisons les discriminations que l’on subit parfois nous même. Nous sommes toustes misogynes, racistes, classistes, grossophobes, validistes, transphobes... car nous vivons dans une société qui l’est et nous ne pouvons pas faire abstraction de cela. L’idée est d''être toujours dans une déconstruction qui ne sera jamais terminée, mais qu’il faut que l’on fasse pour avancer',
		'soeurs-malsaines.com',
		'https://soeurs-malsaines.com/#:~:text=La%20non-discrimination%20des%20personnes%20consommatrices%20de%20produits%20stup%C3%A9fiants.%20La '
		,3, "image-3.jpg"),
		(4,'Soprano', '2025-09-12 19:30:00', '2025-09-12 20:30:00','Moi j''ai pas ', 'Rap',
		'Saïd M''Roumbaba, mieux connu sous le nom de Soprano, né le 13 janvier 1979 à Marseille (Bouches-du-Rhône), est un rappeur, chanteur, compositeur, animateur de télévision et acteur franco-comorien. Soprano débute dans le rap avec le groupe Psy de la rime à la fin des années 1990, courant des années 2000. Par la suite, il se lance dans des projets solo, il sort un premier album en 2007 Puisqu''il faut vivre. En 2013, son album Cosmopolitanie assure un virage pop.',
		'Wikipedia',
		'"https://fr.wikipedia.org/wiki/Soprano_(rappeur)#:~:text=Sa%C3%AFd%20M''Roumbaba,%20mieux%20connu%20sous%20le%20nom%20de%20Soprano,%20n%C3%A9#:~:text=Sa%C3%AFd%20M''Roumbaba,%20mieux%20connu%20sous%20le%20nom%20de%20Soprano,%20n%C3%A9 '
		,4, "image-4.jpg"),
		(5,'Lana Del rey', '2025-09-12 21:20:00','2025-09-12 22:00:00','Video Game','Rock Psychédélique',
		'Elizabeth Woolridge Grant, dite Lana Del Rey, née le 21 juin 1985 à New York, est une auteure-compositrice-interprète et poétesse américaine.Au début de sa carrière, elle a utilisé d''autres pseudonymes : May Jailer, Lana Del Ray, Sparkle Jump Rope Queen et Lizzy Grant. À travers ses chansons, elle explore les thèmes de la tristesse, de la romance tragique, du glamour et de la mélancolie, puisant ses références dans la culture pop, en particulier dans l''Americana des années 1950 et 1960',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Lana_Del_Rey#:~:text=Elizabeth%20Woolridge%20Grant,%20dite%20Lana%20Del%20Rey,%20n%C3%A9e%20le%2021#:~:text=Elizabeth%20Woolridge%20Grant,%20dite%20Lana%20Del%20Rey,%20n%C3%A9e%20le%2021 '
		,4, "image-5.jpg"),
		(6,'la caravane Passe', '2025-09-12 22:00:00','2025-09-12 23:05:00','Rom à Babylone','musique tzigane',
		'La Caravane Passe est un groupe de musique contemporaine français, teintée de folklores tsiganes et nomades. Formé à la fin 2000 par le chanteur, auteur-compositeur et multi-instrumentiste Toma Feterman, il est rejoint en 2001 par Olivier « Llugs » Llugany, tromboniste et joueur de fiscorn catalan, puis par Cyril « Zinzin » Moret au saxophone, Ben Body à la basse et Pat Gigon à la batterie.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/La_Caravane_Passe#:~:text=La%20Caravane%20Passe%20est%20un%20groupe%20de%20musique%20contemporaine%20fran%C3%A7ais, '
		,3, "image-6.jpg"),
		(7,'Burna Boy', '2025-09-12 23:00:00','2025-09-12 00:30:00','Gbona','Afrobeat',
		'Damini Ebunoluwa Ogulu, né le 2 juillet 1991 connu professionnellement sous le nom de Burna Boy est un chanteur et compositeur nigérian. Surnommé Odugwu, Il est devenu célèbre en 2012 après avoir sorti Like to Party, le single principal de son premier album studio LIFE (2012). Ses albums et singles s''enchaînent ensuite. En 2017, Burna Boy a signé avec Bad Habit / Atlantic Records aux États-Unis et Warner Music Group à l''international. Son troisième album studio Outside (2018) a marqué ses débuts sur un grand label. En 2019, il a remporté le prix du meilleur artiste international aux BET Awards 2019. Son quatrième album studio, African Giant, est sorti en juillet 2019, le cinquième, Twice as Tall, en 2020. Il sort son sixième album, Love Damini, en juillet 2022.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Burna_Boy#:~:text=Damini%20Ebunoluwa%20Ogulu,%20n%C3%A9%20le%202%20juillet%201991%20connu%20professionnellement#:~:text=Damini%20Ebunoluwa%20Ogulu,%20n%C3%A9%20le%202%20juillet%201991%20connu%20professionnellement '
		,5, "image-7.jpg"),
		(8,'Charlotte De Witte', '2025-09-12 00:45:00','2025-09-12 02:14:00','ROAR','Techno',
		'Charlotte de Witte est une disc-jockey belge et productrice de musique électronique. Elle est née à Gand le 21 juillet 1991. Elle débute en 2010 sous le pseudonyme de Raving George et se fait connaître, surtout localement.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Charlotte_de_Witte#:~:text=Charlotte%20de%20Witte%20est%20une%20disc-jockey%20belge%20et%20productrice%20de'
		,2,"image-8.jpg"),
		(9,'Yungblud', '2025-09-12 02:10:00','2025-09-12 03:00:00','Abyss','Rock-Alternatif',
		'Dominic Harrison, dit Yungblud (stylisé en YUNGBLUD), est un auteur-compositeur-interprète britannique, né le 5 août 1972 à Doncaster au Royaume-Uni. À travers sa musique, Yungblud s''est exprimé comme un « artiste socialement engagé n''ayant pas peur de délivrer des chansons protestataires mélangeant les genres ». Il commence sa carrière musicale en 2017 et se fait rapidement connaître4, notamment avec son single I Love You, Will You Marry Me. Il a actuellement sorti trois albums : 21st Century Liability en 2018, Weird! en 2020 et Yungblud en 2022. Il se fait également connaître à travers des collaborations. Il collabore avec la chanteuse Charlotte Lawrence sur la chanson Falling Skies, qui apparait sur la bande originale de la deuxième saison de la série 12 Reasons Why5. Il sort également, en collaboration avec Halsey, et Travis Barker, la chanson 11 minutes dont le clip possède plus de 84 millions de vues. En 2022, il réalise une version française de sa chanson Tissues avec la chanteuse Louane',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Yungblud#:~:text=Dominic%20Harrison%201,%20dit%20Yungblud%20(stylis%C3%A9%20en%20YUNGBLUD),%20est%20un#:~:text=Dominic%20Harrison%201,%20dit%20Yungblud%20(stylis%C3%A9%20en%20YUNGBLUD),%20est%20un '
		,5, "image-9.jpg"),
		(10,'Santa', '2025-09-12 13:20:00','2025-09-13 12:40:00','Popcorn salé','néo-varieté',
		'Santa grandit à Nice dans une famille d’artistes entre une mère américaine, chanteuse du groupe Daisy Duck au début des années 801, et un père architecte ayant fait les Beaux-Arts. En 2009, elle crée le groupe Hyphen Hyphen avec ses amis d''enfance, Adam (Romain Adamo), Line (Laura Christin) et Zac (Zaccharie Schütte) et avec lesquels, en 2016, elle remporte la Victoire de la musique Révélation. En 2022, en parallèle de sa carrière avec le groupe, elle se lance dans une expérience solo et sort un EP de 6 titres, 9994, certifié disque d''or. Le premier extrait, Popcorn salé6, rencontre un important succès et est certifié disque de diamant',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Santa_(chanteuse)#:~:text=Samanta%20Cotta,%20dite%20Santa,%20est%20une%20auteure-compositrice-interpr%C3%A8te%20et%20musicienne '
		,1, "image-10.jpg"),
		(11,'Girl In Red', '2025-09-13 13:14:00','2025-09-13 13:35:00','I Wanna Be Your Girlfriend ','Indie-Pop',
		'Elle est connue pour ses bedroom pop anthems about romance and mental healthes hymnes de pop de chambre parlant d''amour et de santé mentale »). Son single I Wanna Be Your Girlfriend a été streamé plus de 140 millions de fois en ligne, et s''est classé numéro 9 sur la liste du New York Times des 68 meilleures chansons de 2018. Depuis 2018, Ulven a sorti deux EP depuis son studio de chambre, et réuni plus de 10 millions de personnes l''écoutant en un mois sur Spotify (mois d''avril 2021). Elle a aussi sorti son premier album, if I could make it go quiet le 30 avril 2021, précédé de deux singles, Serotonin et You Stupid Bitch.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Girl_in_Red#:~:text=Marie%20Ulven%20Ringheim%20dite%20girl%20in%20red%20(en%20minuscules),%20n%C3%A9e '
		,5, "image-11.jpg"),
		(12,'SDM', '2025-09-13 18:10:00','2025-09-13 19:30:00','Bolide Allemand ','Rap',
		'SDM baigne dans la musique depuis tout petit, principalement la rumba congolaise. Son père, décédé en 2017 et avec qui il n''a pas grandi10, est en effet le producteur de Koffi Olomidé, et est de l''entourage de Fally Ipupa4. Sa mère exerce des activités commerciales et humanitaires, avec une association basée à Kinshasa4. A côté de la rumba, SDM écoute également de l''afrobeat et du RNB. Au collège, il découvre le rap grâce à un camarade qui lui fait écouter Ouest Side de Booba11. Une autre de ses idoles est 50 Cent.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/SDM_(rappeur)#:~:text=SDM%20est%20un%20rappeur%20fran%C3%A7ais%20d%E2%80%99origine%20congolaise%20n%C3%A9%20le%2028 '
		,3, "image-12.jpg"),
		(13,'Jain', '2025-09-13 19:05:00','2025-09-13 20:10:00','Alright','Worldbeat',
		'Chantant en anglais, elle développe un style de musique mélangeant la pop avec de nombreux autres genres dont la musique électronique, le reggae et diverses musiques du monde, inspiré par ses nombreux séjours à l''étranger. Son premier album, Zanaka, sorti en novembre 2014 est certifié double disque de platine en septembre 2016. Son deuxième album, Souldier, est sorti en août 2018. The Fool, son troisième album, est sorti en avril 2023.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Jain_(chanteuse)#Voir_aussi'
		,5, "image-13.jpg"),
		(14,'Bruno Mars', '2025-09-13 19:30:00','2025-09-13 20:30:00','Uptown Funk ','Pop funk',
		'Il grandit dans une famille de musiciens et fait ses débuts dans la musique en produisant d''autres artistes, au travers de l''équipe de production The Smeezingtons avec Philip Lawrence et Ari Levine. Il est connu pour ses jeux de , pour sa mise en  rétro et pour ses oeuvres d''une grande variété de styles musicaux, notamment le RnB, le funk, la pop, la soul, le reggae, le hip hop et le rock. Bruno Mars est accompagné de son groupe, The Hooligans, qui joue de nombreux instruments, tels que la guitare électrique, la basse, le piano, les claviers, la batterie et les cuivres et joue également des rôles de danseurs.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Bruno_Mars#:~:text=Peter%20Gene%20Hernandez,%20dit%20Bruno%20Mars,%20n%C3%A9%20le%208%20octobre '
		,4, "image-14.jpg"),
		(15,'Mc Solaar', '2025-09-13 20:30:00','2025-09-13 21:25:00','Solaar pleure ','Rap',
		'Fin 2021, MC Solaar annonce qu''il a commencé à travailler sur son 7e album : « à partir du mois de janvier, pour la première fois de ma vie, je vais être organisé. Il continue de tourner en parallèle, et apparait à cette période dans le clip de Bons élèves avec Bigflo et Oli33. En juin 2023, il chante sur le morceau Tout se transforme, bande originale du film Transformers: Rise of the Beasts, puis collabore avec Vianney pour son album À 2 à 3 qui sort en novembre. C''est finalement deux ans après sa première annonce que MC Solaar fait son retour en solo, à temps pour sa nouvelle grande tournée annoncée pour 202536. Le 12 janvier 2025, il sort le clip Pierre-Feuille, qui sera suivi le 23 février par le morceau Ils dansent. Avec ce dernier, il donne le titre et la date de sortie de son nouvel opus, qui sera un triptyque. Triptyque : Lueurs Célestes sort le 14 mars 2025. Tryptique: Eclats cosmiques sort le 5 juillet 2025.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/MC_Solaar#G%C3%A9opo%C3%A9tique,_l''album_du_retour_(2017) '
		,1, "image-15.jpg"),
		(16,'Stromae', '2025-09-13 21:25:00','2025-09-13 22:05:00','Alors on danse','Pop House',
		'Stromae se fait connaître en 2010 avec la chanson Alors on danse extraite de l''album Cheese. En parallèle, il crée son propre label Mosaert, afin d''assurer la production de ce premier album. Il se fera ensuite remarquer par un style mêlant thèmes mélancoliques et musiques dansantes. En 2012, son deuxième album Racine carrée est un succès critique et commercial majeur, avec des tubes comme Papaoutai, Formidable et Tous les mêmes. La tournée qui suit le fait connaître mondialement, avec plus de deux cents dates dans une vingtaine de pays, regroupant 1,6 million de spectateurs.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Stromae#:~:text=Stromae%20(prononciation%20:%20/st%CA%81omaj/%201,%202),%20nom%20de%20sc%C3%A8ne%20de#:~:text=Stromae%20(prononciation%20:%20/st%CA%81omaj/%201,%202),%20nom%20de%20sc%C3%A8ne%20de '
		,4, "image-16.jpg"),
		(17,'Hoshi', '2025-09-13 21:45:00','2025-09-13 23:20:00','Ta marinière ','Pop-Rock ',
		'Hoshi fait ses premiers pas dans la musique avec le groupe amateur TransyStory formé en septembre 2011. Elle est en classe de seconde et assure le chant, les claviers ainsi que la guitare rythmique4 dans ce quartet qui se produit occasionnellement dans les salles municipales de Montigny-le-Bretonneux5. Le groupe joue notamment Lou, sa première composition enregistrée6. En parallèle, elle se présente seule et remporte en décembre 2011 le concours Jeunes Talents, c’est maintenant ! organisé par le conseil municipal de sa ville7. Elle est repérée à cette occasion par Marc Isacco alors responsable d''une section musicale de Montigny qui lui permet de rencontrer des artistes établis et de jouer dans des salles plus grandes. ',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Hoshi_(chanteuse)#:~:text=Mathilde%20Gerner,%20dite%20Hoshi,%20est%20une%20auteure-compositrice-interpr%C3%A8te%20fran%C3%A7aise,#:~:text=Mathilde%20Gerner,%20dite%20Hoshi,%20est%20une%20auteure-compositrice-interpr%C3%A8te%20fran%C3%A7aise,'
		,3, "image-17.jpg"),
		(18,'Bianca Costa', '2025-09-13 23:00:00','2025-09-13 00:14:00','Olé Olé ','Bossa nova',
		'Bianca Costa naît le 4 mars 1999, à Florianópolis, au Brésil1, elle déménage au Portugal à 5 ans2, et ensuite s''installe avec sa mère en France à l''âge de 10 ans3. Elle grandit sans son père, parti alors qu''elle avait 4 ans4. Elle tient sa fibre musicale de son grand-père qui est lui même musicien2 et des chants d''églises qu''elle a appris au sein de sa famille très religieuse2. Elle partage son histoire dans son single, Partout & nulle part. Elle dit: « Cette chanson est très importante pour moi. Non seulement j’y parle explicitement de mon enfance, mais surtout, c’est la première chanson que j’interprète exclusivement en français.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Bianca_Costa#:~:text=Bianca%20Costa,%20n%C3%A9e%20le%204%20mars%201999%20%C3%A0%20Florian%C3%B3polis%20(Br%C3%A9sil),'
		,2, "image-18.jpg"),
		(19,'La Fève', '2025-09-13 00:50:00','2025-09-13 02:25:00','BALENCIAGA','Rap',
		'La Fève, de son vrai nom Louis Ambroise, né le 26 juin 1999 dans le 20e arrondissement de Paris, est un rappeur et producteur français. En 2021, il est un membre du mouvement musical new wave du hip-hop français. Sa musique est jugée innovante, utilisant une sélection éclectique de flows, productions et refrains',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/La_F%C3%A8ve#:~:text=La%20F%C3%A8ve,%20de%20son%20vrai%20nom%20Louis%20Ambroise,%20n%C3%A9%20le '
		,1, "image-19.jpg"),
		(20,'Trinix', '2025-09-13 01:30:00','2025-09-13 03:00:00','Sweet Dreams','Mixage',
		'Trinix est un groupe de musique électronique français originaire de Lyon dans le Rhône, composé de Josh Chergui et de Lois Serre1. Depuis 2016, ils parcourent les s de plusieurs festivals français, comme Garorock en 2017, et se produisent également à l’étranger dont aux États-Unis. Vers cette période, ils enregistrent un EP2. En 2018, ils sortent l''album de 20 titres Mayday enregistré en partie à Los Angeles, et distribué par label Sony Music.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Trinix#:~:text=Trinix%20est%20un%20groupe%20de%20musique%20%C3%A9lectronique%20fran%C3%A7ais%20originaire%20de '
		,2, "image-20.jpg"),
		(21,'Lucky Love', '2025-09-14 13:00:00','2025-09-14 14:20:00','I''M READY ','pop ',
		'Il a subjugué tous les spectateurs et téléspectateurs, mercredi 28 août, lors de la cérémonie d’ouverture des Jeux paralympiques à Paris. Avec ses faux airs de Freddie Mercury que surlignent ses lunettes aviator fumées et sa moustache, le chanteur et mannequin Lucky Love a offert une performance bouleversante, magnifique, qui restera dans les rétines et les esprits. Alors que les athlètes de tous les pays étaient enfin arrivés sur la place de la Concorde, Luc Bruyère à l’état civil a interprété, dans un tableau lumineux, sa chanson en anglais My Ability, nouvelle version de son bientôt tube Masculinity.',
		'Wikipedia',
		'https://www.francetvinfo.fr/culture/musique/pop/chanteur-danseur-mannequin-qui-est-lucky-love-l-artiste-aux-mille-vies-qui-a-envoute-la-ceremonie-d-ouverture-des-jeux-paralympiques_6751395.html#:~:text=A%2030%20ans,%20Lucky%20Love,%20n%C3%A9%20sans%20bras%20gauche,%20s''est '
		,5, "image-21.jpg"),		
		(22,'Slimane', '2025-09-14 14:20:00','2025-09-14 17:40:00','Mon amour ','Pop RnB ',
		'Adolescent, en 2002, Slimane Nebchi se découvre une passion pour le chant en intégrant une chorale gospel. Après avoir passé son bac, il intègre plusieurs écoles de musique pendant trois ans7 et se produit dans des piano-bars de Pigalle8. Il participe à divers télé-crochets : Popstar en 20079, Nouvelle Star en 2009, et X-Factor en 2011 sur M6 et Encore une chance en 2012 sur NRJ 1210. Bien qu''il ne remporte aucun de ces concours, il commence à se faire remarquer et poste également des vidéos sur Youtube à partir de 2009, où il interprète ses premières compositions. En 2014, dans le bar de nuit l''Orphée de Pigalle où il se produit régulièrement, il y fait la rencontre d''Éric Dussart qui le met en contact avec Didier Barbelivien à la recherche d''une voix cassée pour sa prochaine comédie musicale, Marie-Antoinette et le Chevalier de Maison-Rouge. Barbelivien l''engage dans la troupe de cette comédie musicale et le fait enregistrer le premier titre de sa carrière. Mais faute de diffusion sur les ondes, ce projet est ajourné avant d''être monté',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Slimane_(chanteur)#:~:text=Slimane%20Nebchi,%20dit%20Slimane,%20est%20un%20auteur-compositeur-interpr%C3%A8te%20et%20acteur#:~:text=Slimane%20Nebchi,%20dit%20Slimane,%20est%20un%20auteur-compositeur-interpr%C3%A8te%20et%20acteur '
		,1, "image-22.jpg"),
		(23,'SCH', '2025-09-14 17:00:00','2025-09-14 18:20:00','Interlude','Rap',
		'SCH dit appartenir à la génération d''Eminem et 50 Cent mais reconnaît aussi l''influence des disques de son père, comme Joe Dassin ou Elton John. Il commence le rap vers 12 ans après avoir vu le film 8 Mile qu''il qualifie de \"traumatisant\", sous le pseudonyme « Schneider », en postant ses premières chansons sur un Skyblog. La veille d''une épreuve du baccalauréat, il est agressé à coup de tesson de bouteille et sera recalé. Il entrera par la suite en section professionnelle Technicien du froid et du conditionnement de l''air et enchaînera les petits boulots. Il optera en 2013 pour « SCH » : les trois lettres initiales de son premier alias (« Schneider ») comme de son nom de famille (« Schwarzer »). Il se fait connaître en 2014 en apparaissant sur la mixtape R.I.P.R.O.',
		'francetinfo',
		'https://fr.wikipedia.org/wiki/SCH_(rappeur)#:~:text=SCH,%20de%20son%20vrai%20nom%20Julien%20Schwarzer%202,%20est%20un#:~:text=SCH,%20de%20son%20vrai%20nom%20Julien%20Schwarzer%202,%20est%20un '
		,5, "image-23.jpg"),
		(24,'Psycotic Monk', '2025-09-14 17:30:00','2025-09-14 18:00:00','Talking through repetition ','Pop',
		'Le rock psychédélique des parisiens est empreint de bruit et de fureur. Et puis d’un coup, la lumière jaillit comme une série B des années 60. Un paradoxe qui fait le charme de cette formation créée par Arthur Dussaux et Martin Bejuy, deux copains de bacs à sable. En revanche, c’est avec l’arrivée de Clément Caillierez fraîchement débarqué de sa province natale que le groupe commence à prendre forme.',
		'rockmadeinfrance',
		'https://www.rockmadeinfrance.com/encyclo/the-psychotic-monks/23421/'
		,3, "image-24.jpg"),
		(25,'Meute', '2025-09-14 19:30:00','2025-09-14 20:30:00','vermis','Electro',
		'"Meute (également typographié MEUTE) est une fanfare techno composée de onze musiciens (percussions et cuivres) de Hambourg, en Allemagne. Le groupe reprend et arrange des œuvres techno, house et deep house par des DJ bien connus, les augmentant avec des rythmes électroniques créés par des instruments de fanfare. Ce faisant, ils cherchent à créer « un nouveau genre en combinant une techno de conduite hypnotique et une musique de fanfare expressive », « détachant ainsi la musique électronique des platines du DJ ». Meute a fait des tournées en Europe, en Amérique et en Afrique australe et s''est produit dans une variété de lieux, notamment des rues de villes, des festivals de musique et des salles de concert classiques. Meute (également typographié MEUTE) est une fanfare techno composée de onze musiciens (percussions et cuivres) de Hambourg, en Allemagne. Le groupe reprend et arrange des œuvres techno, house et deep house par des DJ bien connus, les augmentant avec des rythmes électroniques créés par des instruments de fanfare. Ce faisant, ils cherchent à créer un nouveau genre en combinant une techno de conduite hypnotique et une musique de fanfare expressive, détachant ainsi la musique électronique des platines du DJ.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Meute_(fanfare)'
		,3, "image-25.jpg"),
		(26,'Eddy de Pretto', '2025-09-14 20:25:00','2025-09-14 21:45:00','Bateaux-Mouches','Electro-pop ',
		'Eddy de Pretto fait ses premiers pas d''acteur en commençant par la publicité télévisée, notamment dans le rôle du jeune Jules César pour CanalSat en septembre 201012,13, avant de débuter dans le monde théâtral.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Meute_(fanfare)#:~:text=Meute%20(%C3%A9galement%20typographi%C3%A9%20MEUTE)%20est%20une%20fanfare%20techno%20compos%C3%A9e%20de#:~:text=Meute%20(%C3%A9galement%20typographi%C3%A9%20MEUTE)%20est%20une%20fanfare%20techno%20compos%C3%A9e%20de '
		,1, "image-26.jpg"),
		(27,'Big Flo & Oli', '2025-09-14 21:20:00','2025-09-14 22:00:00','Ici c’est Toulouse ','Rap',
		'Bigflo et Oli est un groupe français de rap, originaire de Toulouse. Le duo est composé des frères Florian « Bigflo » et Olivio « Oli » Ordoñez. Ils sortent leur premier album La Cour des grands en 2014, qui est certifié disque d''or moins de quatre mois après sa sortie puis disque de platine en France. Ils sont alors les plus jeunes rappeurs français à détenir ces récompenses. Leur deuxième album intitulé La Vraie Vie, sort le 23 juin 2017. Il est certifié disque de diamant. Leur troisième album, La Vie de rêve, sort en 2018, est certifié disque d''or en dix jours d''exploitation puis disque de platine3 un mois après sa sortie. En mai 2020, il est certifié triple disque de platine. Après une pause médiatique de deux ans, le duo revient avec un quatrième album en 2022, Les autres c''est nous. Dans la même année, le duo est nommé Groupe/Duo Francophone de l’année, l''un des gagnants des NRJ Music Awards 20224.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Bigflo_et_Oli#:~:text=Bigflo%20et%20Oli%20est%20un%20groupe%20fran%C3%A7ais%20de%20rap,%20originaire '
		,4, "image-27.jpg"),
		(28,'Daft Punk', '2025-09-14 23:00:00','2025-09-14 00:30:00','Harder, Better, Faster, Stronger ','Electro House ',
		'Le duo a  composé en 2010 la bande son du film Tron : L''Héritage. En 2012, Daft Punk quitte EMI pour signer avec le label Columbia Records et sortir un album intitulé Random Access Memories, qui remporte un important succès international et six Grammy Awards, dont celui du meilleur album de l''année, alors que la chanson Get Lucky triomphe dans le monde entier. Le 22 février 2021, ils annoncent la séparation de leur duo dans une vidéo intitulée Epilogue, après vingt-huit ans de carrière.',
		'Wikipedia',
		'https://fr.wikipedia.org/wiki/Daft_Punk#:~:text=Daft%20Punk%20(prononc%C3%A9%20en%20anglais%20:%20[d%C3%A6ft%20p%CA%8C%C5%8Bk])%20est%20un#:~:text=Daft%20Punk%20(prononc%C3%A9%20en%20anglais%20:%20[d%C3%A6ft%20p%CA%8C%C5%8Bk])%20est%20un'
		,4, "image-28.jpg"),
		(29,'Sierra', '2025-09-14 00:00:00','2025-09-14 01:00:00','A Story of Anger','Rap',
		'Récemment, SIERRA nous a présenté son tout premier album A Story of Anger, un opus de quarante-deux minutes qui présente avec violence, mélancolie, douceur et brutalité son univers. Depuis sa sortie le 14 septembre dernier, l’artiste défend avec énergie son projet en traversant les Etats-Unis, avant de regagner l’Europe au début du mois d’octobre.',
		'whatthefrance',
		'https://whatthefrance.org/fr/sierra-un-premier-album-une-tournee-internationale-et-une-playlist-exclusive/ '
		,2, "image-29.jpg"),
		(30,'Aotem', '2025-09-14 01:30:00','2025-09-14 03:00:00','Ghosts of The Past','Electro',
		'À rebours de l’époque où le pied frappe fort sur la plupart des dancefloors, Antoine et Gabriel s’épanouissent dans une certaine lenteur. « Faire de la musique, c’est travailler avec le silence. » Certains morceaux peuvent descendre jusqu’à 85 BPM, un tempo naturel comme le battement du coeur, pour donner plus d’espace aux arrangements. On a hâte de retrouver nos anciens chouchous du Label Charrues  Grall !',
		'vieillescharrues',
		'https://www.vieillescharrues.asso.fr/programmation/#/detail/atoem '
		,2, "image-30.jpg");

SELECT *
FROM artist;
            






