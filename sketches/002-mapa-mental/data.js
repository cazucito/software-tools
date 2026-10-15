// Generado por tools/prep_data.py desde catalog.sqlite — no editar a mano.
window.ST_DATA = {
 "site": "software-tools",
 "categories": [
  {
   "slug": "database",
   "label": "Bases de datos",
   "count": 4,
   "subs": []
  },
  {
   "slug": "editor",
   "label": "Editores",
   "count": 10,
   "subs": [
    {
     "slug": "ides",
     "label": "IDEs",
     "count": 5
    },
    {
     "slug": "editores-de-codigo",
     "label": "Editores de código",
     "count": 3
    },
    {
     "slug": "escritura-cientifica",
     "label": "Escritura científica",
     "count": 2
    }
   ]
  },
  {
   "slug": "infra",
   "label": "Infraestructura",
   "count": 4,
   "subs": []
  },
  {
   "slug": "lenguaje",
   "label": "Lenguajes",
   "count": 7,
   "subs": [
    {
     "slug": "borland",
     "label": "Borland",
     "count": 4
    },
    {
     "slug": "basic",
     "label": "BASIC",
     "count": 3
    }
   ]
  },
  {
   "slug": "os",
   "label": "Sistemas operativos",
   "count": 3,
   "subs": []
  },
  {
   "slug": "tool",
   "label": "Herramientas",
   "count": 29,
   "subs": [
    {
     "slug": "navegadores",
     "label": "Navegadores",
     "count": 4
    },
    {
     "slug": "correo",
     "label": "Correo",
     "count": 2
    },
    {
     "slug": "archivos-y-respaldo",
     "label": "Archivos y respaldo",
     "count": 6
    },
    {
     "slug": "diagramacion",
     "label": "Diagramación y conocimiento",
     "count": 6
    },
    {
     "slug": "calculo-e-ingenieria",
     "label": "Cálculo e ingeniería",
     "count": 5
    },
    {
     "slug": "ofimatica",
     "label": "Ofimática",
     "count": 2
    },
    {
     "slug": "arte-ascii",
     "label": "Arte ASCII",
     "count": 2
    },
    {
     "slug": "mantenimiento",
     "label": "Mantenimiento",
     "count": 1
    },
    {
     "slug": "agentes-ia",
     "label": "Agentes de IA",
     "count": 1
    }
   ]
  }
 ],
 "tools": [
  {
   "slug": "ms-dos-5-1",
   "name": "MS-DOS 5.1",
   "year": 1991,
   "until": 1997,
   "cat": "os",
   "sub": null,
   "ctx": "El sistema operativo donde aprendí que la computadora hace exactamente lo que le dices, no lo que quieres.",
   "tags": [
    "dos",
    "os",
    "command-line",
    "batch",
    "memory-management"
   ],
   "rel": [
    "turbo-c",
    "chi-writer",
    "bannermania",
    "borland-pascal-7",
    "figlet",
    "lotus-1-2-3",
    "norton-commander",
    "norton-ghost",
    "pkzip",
    "qbasic",
    "windows-95",
    "windows-98"
   ],
   "succ": "windows-95"
  },
  {
   "slug": "pkzip",
   "name": "PKZIP",
   "year": 1991,
   "until": 1998,
   "cat": "tool",
   "sub": "archivos-y-respaldo",
   "ctx": "El estándar de compresión que definió el formato ZIP. Antes de WinZip, antes de Windows, estaba Phil Katz y su PKZIP en línea de comandos.",
   "tags": [
    "compression",
    "zip",
    "dos",
    "file-format",
    "phil-katz"
   ],
   "rel": [
    "ms-dos-5-1",
    "norton-commander",
    "winzip"
   ],
   "succ": "winzip"
  },
  {
   "slug": "qbasic",
   "name": "QBasic",
   "year": 1991,
   "until": 1993,
   "cat": "lenguaje",
   "sub": "basic",
   "ctx": "Mi primera experiencia con programación. El intérprete incluido en DOS que me enseñó qué era un bucle, una variable, y la satisfacción de hacer que la computadora hiciera lo que yo quería.",
   "tags": [
    "basic",
    "microsoft",
    "dos",
    "learning",
    "beginner"
   ],
   "rel": [
    "ms-dos-5-1",
    "turbo-c"
   ],
   "succ": "quickbasic-4-5"
  },
  {
   "slug": "lotus-1-2-3",
   "name": "Lotus 1-2-3",
   "year": 1992,
   "until": 1996,
   "cat": "tool",
   "sub": "ofimatica",
   "ctx": "La hoja de cálculo dominante antes de Excel. Aprendí a hacer presupuestos y análisis de datos en la interfaz de menús de Lotus.",
   "tags": [
    "spreadsheet",
    "dos",
    "lotus",
    "business",
    "calculations"
   ],
   "rel": [
    "ms-dos-5-1",
    "windows-95",
    "microsoft-excel"
   ],
   "succ": "microsoft-excel"
  },
  {
   "slug": "norton-commander",
   "name": "Norton Commander",
   "year": 1992,
   "until": 1998,
   "cat": "tool",
   "sub": "archivos-y-respaldo",
   "ctx": "El gestor de archivos que hizo obsoleto el comando DIR. Dos paneles, comandos rápidos, y la interfaz azul legendaria que aún imitan los file managers modernos.",
   "tags": [
    "file-manager",
    "dos",
    "norton",
    "dual-pane",
    "utilities"
   ],
   "rel": [
    "ms-dos-5-1",
    "pkzip",
    "norton-ghost",
    "windows-explorer"
   ],
   "succ": "windows-explorer"
  },
  {
   "slug": "turbo-c",
   "name": "Turbo C",
   "year": 1992,
   "until": 1996,
   "cat": "lenguaje",
   "sub": "borland",
   "ctx": "Mi primer entorno de desarrollo C. Aprendí punteros, memoria y estructuras en esa pantalla azul con texto amarillo.",
   "tags": [
    "c",
    "dos",
    "compiler",
    "borland",
    "ide"
   ],
   "rel": [
    "ms-dos-5-1",
    "chi-writer",
    "borland-cpp",
    "borland-pascal-7",
    "delphi",
    "qbasic"
   ],
   "succ": "borland-cpp"
  },
  {
   "slug": "bannermania",
   "name": "BannerMania",
   "year": 1993,
   "until": 1996,
   "cat": "tool",
   "sub": "arte-ascii",
   "ctx": "El programa para hacer banners ASCII que usaba en todas las impresiones de la facultad. Mi firma digital antes de que existiera la firma digital.",
   "tags": [
    "ascii-art",
    "text-graphics",
    "dos",
    "banners",
    "fun"
   ],
   "rel": [
    "ms-dos-5-1",
    "chi-writer",
    "figlet"
   ],
   "succ": "figlet"
  },
  {
   "slug": "borland-pascal-7",
   "name": "Borland Pascal 7.0",
   "year": 1993,
   "until": 1996,
   "cat": "lenguaje",
   "sub": "borland",
   "ctx": "La evolución de Turbo Pascal con soporte para objetos. Aprendí programación orientada a objetos con este IDE azul legendaria.",
   "tags": [
    "pascal",
    "dos",
    "ide",
    "borland",
    "oop",
    "objects"
   ],
   "rel": [
    "turbo-c",
    "ms-dos-5-1",
    "borland-cpp",
    "delphi"
   ],
   "succ": "delphi"
  },
  {
   "slug": "maple-v",
   "name": "Maple V",
   "year": 1993,
   "until": 1997,
   "cat": "tool",
   "sub": "calculo-e-ingenieria",
   "ctx": "La alternativa canadiense a Mathematica. Más barata, más accesible, y con capacidades simbólicas comparables. Mi herramienta de matemáticas simbólicas preferida.",
   "tags": [
    "maple",
    "symbolic",
    "math",
    "waterloo",
    "algebra"
   ],
   "rel": [
    "mathematica",
    "matlab"
   ],
   "succ": "maple-6"
  },
  {
   "slug": "eudora",
   "name": "Eudora",
   "year": 1994,
   "until": 1998,
   "cat": "tool",
   "sub": "correo",
   "ctx": "Mi primer cliente de email serio. En tiempos donde Outlook no existía, Eudora era el estándar para gestionar correo electrónico por POP3.",
   "tags": [
    "email",
    "pop3",
    "smtp",
    "client",
    "internet"
   ],
   "rel": [
    "netscape-navigator",
    "windows-95",
    "microsoft-outlook"
   ],
   "succ": "microsoft-outlook"
  },
  {
   "slug": "matlab",
   "name": "MATLAB",
   "year": 1994,
   "until": 2000,
   "cat": "tool",
   "sub": "calculo-e-ingenieria",
   "ctx": "El estándar para computación numérica en ingeniería. Resolví sistemas de ecuaciones, hice simulaciones y grafiqué funciones complejas sin escribir código de bajo nivel.",
   "tags": [
    "matlab",
    "math",
    "engineering",
    "simulation",
    "matrix"
   ],
   "rel": [
    "mathematica",
    "maple-v",
    "electronic-workbench",
    "latex",
    "multisim"
   ],
   "succ": "matlab-r14"
  },
  {
   "slug": "chi-writer",
   "name": "Chi Writer",
   "year": 1995,
   "until": 1998,
   "cat": "editor",
   "sub": "escritura-cientifica",
   "ctx": "Editor científico usado para mi tesis de licenciatura, especialmente valioso por su manejo correcto de caracteres chinos.",
   "tags": [
    "dos",
    "scientific-writing",
    "thesis",
    "chinese-characters",
    "word-processor"
   ],
   "rel": [
    "ms-dos-5-1",
    "turbo-c",
    "bannermania",
    "latex"
   ],
   "succ": "latex"
  },
  {
   "slug": "mathematica",
   "name": "Mathematica",
   "year": 1995,
   "until": 1998,
   "cat": "tool",
   "sub": "calculo-e-ingenieria",
   "ctx": "Computación simbólica que parecía magia. Resolver integrales simbólicamente, simplificar expresiones algebraicas complejas, y obtener resultados exactos en lugar de aproximaciones numéricas.",
   "tags": [
    "mathematica",
    "symbolic",
    "math",
    "wolfram",
    "algebra"
   ],
   "rel": [
    "matlab",
    "maple-v",
    "electronic-workbench",
    "latex"
   ],
   "succ": "mathematica-3"
  },
  {
   "slug": "netscape-navigator",
   "name": "Netscape Navigator",
   "year": 1995,
   "until": 1998,
   "cat": "tool",
   "sub": "navegadores",
   "ctx": "Mi primer navegador web. La ventana al mundo de Internet en tiempos donde la conexión era por modem y hacías cola para usar la computadora con Internet en la facultad.",
   "tags": [
    "browser",
    "web",
    "netscape",
    "internet",
    "mosaic"
   ],
   "rel": [
    "windows-95",
    "eudora",
    "internet-explorer",
    "mozilla-firefox"
   ],
   "succ": "internet-explorer"
  },
  {
   "slug": "windows-95",
   "name": "Windows 95",
   "year": 1995,
   "until": 1998,
   "cat": "os",
   "sub": null,
   "ctx": "El sistema operativo que mató a DOS. La transición de la línea de comandos a la interfaz gráfica fue traumática y emocionante a la vez.",
   "tags": [
    "windows",
    "microsoft",
    "gui",
    "32-bit",
    "plug-and-play"
   ],
   "rel": [
    "ms-dos-5-1",
    "visual-basic-6",
    "eudora",
    "internet-explorer",
    "lotus-1-2-3",
    "netscape-navigator",
    "vmware-workstation",
    "windows-98"
   ],
   "succ": "windows-98"
  },
  {
   "slug": "borland-cpp",
   "name": "Borland C++",
   "year": 1996,
   "until": null,
   "cat": "lenguaje",
   "sub": "borland",
   "ctx": "El siguiente paso de Turbo C. Un C++ completo de Borland para cuando los proyectos crecieron más allá del C puro.",
   "tags": [
    "c",
    "cpp",
    "borland",
    "ide",
    "programming"
   ],
   "rel": [
    "turbo-c",
    "borland-pascal-7"
   ],
   "succ": null
  },
  {
   "slug": "delphi",
   "name": "Delphi",
   "year": 1996,
   "until": null,
   "cat": "lenguaje",
   "sub": "borland",
   "ctx": "La evolución de Borland Pascal hacia RAD. El mismo Pascal que aprendí, ahora con componentes visuales y compilador nativo.",
   "tags": [
    "pascal",
    "borland",
    "ide",
    "windows",
    "rapid"
   ],
   "rel": [
    "borland-pascal-7",
    "turbo-c",
    "visual-basic-6"
   ],
   "succ": null
  },
  {
   "slug": "electronic-workbench",
   "name": "Electronic Workbench",
   "year": 1996,
   "until": 2000,
   "cat": "tool",
   "sub": "calculo-e-ingenieria",
   "ctx": "El laboratorio de electrónica virtual. Armaba circuitos en la pantalla, conectaba osciloscopios virtuales, y veía el comportamiento antes de soldar componentes reales.",
   "tags": [
    "circuit",
    "simulation",
    "electronics",
    "spice",
    "engineering"
   ],
   "rel": [
    "matlab",
    "mathematica",
    "multisim"
   ],
   "succ": "multisim"
  },
  {
   "slug": "figlet",
   "name": "FIGlet",
   "year": 1996,
   "until": null,
   "cat": "tool",
   "sub": "arte-ascii",
   "ctx": "El arte ASCII hecho programa. En el mundo Unix encontré lo que Bannermania hacía en DOS: letras grandes desde la terminal.",
   "tags": [
    "ascii-art",
    "terminal",
    "unix",
    "banners"
   ],
   "rel": [
    "bannermania",
    "ms-dos-5-1"
   ],
   "succ": null
  },
  {
   "slug": "microsoft-excel",
   "name": "Microsoft Excel",
   "year": 1996,
   "until": null,
   "cat": "tool",
   "sub": "ofimatica",
   "ctx": "La hoja de cálculo que reemplazó a Lotus 1-2-3 en mi trabajo. Cuando la industria migró a Windows, mis presupuestos migraron con ella.",
   "tags": [
    "spreadsheet",
    "microsoft",
    "office",
    "business"
   ],
   "rel": [
    "lotus-1-2-3",
    "microsoft-outlook",
    "ms-access"
   ],
   "succ": null
  },
  {
   "slug": "norton-ghost",
   "name": "Norton Ghost",
   "year": 1996,
   "until": 2005,
   "cat": "tool",
   "sub": "archivos-y-respaldo",
   "ctx": "El salvavidas de los administradores de sistemas. Crear imágenes exactas de discos duros y restaurarlas en minutos era magia en la época.",
   "tags": [
    "backup",
    "imaging",
    "disk-cloning",
    "norton",
    "recovery"
   ],
   "rel": [
    "ms-dos-5-1",
    "norton-commander",
    "acronis-true-image",
    "vmware-workstation",
    "windows-98"
   ],
   "succ": "acronis-true-image"
  },
  {
   "slug": "axon-idea-processor",
   "name": "Axon Idea Processor",
   "year": 1998,
   "until": null,
   "cat": "tool",
   "sub": "diagramacion",
   "ctx": "Procesador de ideas en 3-D escrito en Prolog por Axon Research (Singapur): mapas mentales y conceptuales con color, forma y profundidad. Edición 2004.",
   "tags": [
    "ideas",
    "mapas-mentales",
    "prolog",
    "visualización",
    "shareware",
    "windows"
   ],
   "rel": [
    "mindmanager",
    "thebrain-8"
   ],
   "succ": null
  },
  {
   "slug": "internet-explorer",
   "name": "Internet Explorer",
   "year": 1998,
   "until": 2004,
   "cat": "tool",
   "sub": "navegadores",
   "ctx": "El navegador que venía con Windows y dominó los primeros años de la web masiva. Llegó cuando Netscape empezó a ceder.",
   "tags": [
    "browser",
    "microsoft",
    "internet",
    "web"
   ],
   "rel": [
    "netscape-navigator",
    "mozilla-firefox",
    "windows-95",
    "windows-98",
    "google-chrome"
   ],
   "succ": "mozilla-firefox"
  },
  {
   "slug": "latex",
   "name": "LaTeX",
   "year": 1998,
   "until": null,
   "cat": "editor",
   "sub": "escritura-cientifica",
   "ctx": "El sistema de composición con el que escribir ciencia dejó de ser un problema de formato. Sucesor natural de Chi Writer en mi camino académico.",
   "tags": [
    "latex",
    "tex",
    "scientific-writing",
    "typesetting"
   ],
   "rel": [
    "chi-writer",
    "mathematica",
    "matlab"
   ],
   "succ": null
  },
  {
   "slug": "ms-access",
   "name": "Microsoft Access",
   "year": 1998,
   "until": null,
   "cat": "database",
   "sub": null,
   "ctx": "La base de datos de escritorio de Office. Rápida para prototipos y formularios, compañera natural de Visual Basic.",
   "tags": [
    "database",
    "microsoft",
    "office",
    "desktop"
   ],
   "rel": [
    "visual-basic-6",
    "microsoft-excel",
    "oracle-database"
   ],
   "succ": null
  },
  {
   "slug": "microsoft-outlook",
   "name": "Microsoft Outlook",
   "year": 1998,
   "until": null,
   "cat": "tool",
   "sub": "correo",
   "ctx": "El relevo de Eudora en mi escritorio. Outlook juntó correo, calendario y contactos en una sola aplicación.",
   "tags": [
    "email",
    "microsoft",
    "office",
    "exchange",
    "client"
   ],
   "rel": [
    "eudora",
    "microsoft-excel"
   ],
   "succ": null
  },
  {
   "slug": "mindmanager",
   "name": "MindManager",
   "year": 1998,
   "until": null,
   "cat": "tool",
   "sub": "diagramacion",
   "ctx": "El estándar corporativo de los mapas mentales (Mindjet, hoy Corel): planear y presentar información en mapas interactivos. Edición 2020.",
   "tags": [
    "mapas-mentales",
    "gestión",
    "productividad",
    "windows",
    "comercial"
   ],
   "rel": [
    "axon-idea-processor",
    "personal-brain",
    "thebrain-8"
   ],
   "succ": null
  },
  {
   "slug": "personal-brain",
   "name": "PersonalBrain",
   "year": 1998,
   "until": null,
   "cat": "tool",
   "sub": "diagramacion",
   "ctx": "El «brain» como base de conocimiento personal: pensamientos en red con foco dinámico. La herramienta de TheBrain Technologies antes de llamarse TheBrain.",
   "tags": [
    "mapas-mentales",
    "conocimiento",
    "bases-de-conocimiento",
    "windows"
   ],
   "rel": [
    "thebrain-8",
    "mindmanager"
   ],
   "succ": "thebrain-8"
  },
  {
   "slug": "visual-basic-6",
   "name": "Visual Basic 6.0",
   "year": 1998,
   "until": 2002,
   "cat": "lenguaje",
   "sub": "basic",
   "ctx": "RAD (Rapid Application Development) en su máxima expresión. Arrastrar, soltar, y tener una aplicación Windows funcionando en minutos. Revolucionario para la época.",
   "tags": [
    "visual-basic",
    "microsoft",
    "windows",
    "rapid-development",
    "gui"
   ],
   "rel": [
    "windows-95",
    "ms-access",
    "delphi",
    "visual-basic-net",
    "visual-studio"
   ],
   "succ": "visual-basic-net"
  },
  {
   "slug": "winzip",
   "name": "WinZip",
   "year": 1998,
   "until": null,
   "cat": "tool",
   "sub": "archivos-y-respaldo",
   "ctx": "El ZIP con ventanas. Cuando dejé el DOS atrás, WinZip hizo cómodo comprimir y descomprimir sin memorizar comandos.",
   "tags": [
    "zip",
    "compression",
    "windows",
    "utility"
   ],
   "rel": [
    "pkzip",
    "windows-98"
   ],
   "succ": null
  },
  {
   "slug": "windows-98",
   "name": "Windows 98",
   "year": 1998,
   "until": null,
   "cat": "os",
   "sub": null,
   "ctx": "El Windows de la transición. Con USB y FAT32, fue la base donde vivieron mis herramientas de finales de los 90.",
   "tags": [
    "windows",
    "os",
    "microsoft",
    "desktop",
    "usb"
   ],
   "rel": [
    "windows-95",
    "norton-ghost",
    "vmware-workstation",
    "ms-dos-5-1",
    "internet-explorer",
    "windows-explorer",
    "winzip"
   ],
   "succ": null
  },
  {
   "slug": "windows-explorer",
   "name": "Windows Explorer",
   "year": 1998,
   "until": null,
   "cat": "tool",
   "sub": "archivos-y-respaldo",
   "ctx": "El gestor de archivos que vino con Windows y jubiló a Norton Commander. Menos comandos, más clics — y estaba en todas partes.",
   "tags": [
    "file-manager",
    "windows",
    "microsoft",
    "shell"
   ],
   "rel": [
    "norton-commander",
    "windows-98"
   ],
   "succ": null
  },
  {
   "slug": "vmware-workstation",
   "name": "VMware Workstation",
   "year": 1999,
   "until": 2015,
   "cat": "infra",
   "sub": null,
   "ctx": "La revolución de la virtualización x86. Correr Windows dentro de Windows, o Linux dentro de Windows, sin reiniciar. Cambió mi forma de probar software y sistemas operativos.",
   "tags": [
    "virtualization",
    "vmware",
    "virtual-machine",
    "x86",
    "testing"
   ],
   "rel": [
    "windows-95",
    "norton-ghost",
    "acronis-true-image",
    "docker",
    "kubernetes",
    "virtualbox",
    "windows-98"
   ],
   "succ": "virtualbox"
  },
  {
   "slug": "multisim",
   "name": "Multisim",
   "year": 2000,
   "until": null,
   "cat": "tool",
   "sub": "calculo-e-ingenieria",
   "ctx": "La continuación de Electronics Workbench tras su integración a National Instruments. El mismo laboratorio virtual, en otra casa.",
   "tags": [
    "electronics",
    "simulation",
    "spice",
    "circuit-design"
   ],
   "rel": [
    "electronic-workbench",
    "matlab"
   ],
   "succ": null
  },
  {
   "slug": "netbeans",
   "name": "NetBeans",
   "year": 2000,
   "until": 2018,
   "cat": "editor",
   "sub": "ides",
   "ctx": "El IDE oficial de Sun (luego Oracle). Usado intensivamente durante mis años de docencia de cursos Oracle. Maven integrado y soporte Java EE nativo.",
   "tags": [
    "ide",
    "java",
    "sun",
    "oracle",
    "open-source"
   ],
   "rel": [
    "gel",
    "eclipse",
    "oracle-sql-developer",
    "intellij-idea",
    "oracle-database"
   ],
   "succ": "intellij-idea"
  },
  {
   "slug": "oracle-database",
   "name": "Oracle Database",
   "year": 2000,
   "until": null,
   "cat": "database",
   "sub": null,
   "ctx": "El estándar empresarial que estudié y enseñé durante mis años de docencia Oracle. La referencia de las bases de datos relacionales.",
   "tags": [
    "database",
    "oracle",
    "sql",
    "enterprise"
   ],
   "rel": [
    "oracle-sql-developer",
    "netbeans",
    "datagrip",
    "ms-access"
   ],
   "succ": null
  },
  {
   "slug": "system-mechanic",
   "name": "System Mechanic",
   "year": 2000,
   "until": null,
   "cat": "tool",
   "sub": "mantenimiento",
   "ctx": "Suite de mantenimiento de Windows de iolo technologies: registro, disco y rendimiento. Se conserva el instalador de la versión 3.2d (2000).",
   "tags": [
    "windows",
    "mantenimiento",
    "utilidades",
    "iolo",
    "comercial"
   ],
   "rel": [],
   "succ": null
  },
  {
   "slug": "eclipse",
   "name": "Eclipse",
   "year": 2001,
   "until": 2009,
   "cat": "editor",
   "sub": "ides",
   "ctx": "Mi IDE de Java durante 8 años. De lo mejor del software libre, aunque eventualmente el peso de los plugins y la lentitud me hicieron buscar alternativas.",
   "tags": [
    "ide",
    "java",
    "ibm",
    "open-source",
    "plugins"
   ],
   "rel": [
    "gel",
    "netbeans",
    "visual-studio",
    "intellij-idea",
    "umlet"
   ],
   "succ": "intellij-idea"
  },
  {
   "slug": "umlet",
   "name": "UMLet",
   "year": 2001,
   "until": null,
   "cat": "tool",
   "sub": "diagramacion",
   "ctx": "Editor UML minimalista y open source: diagramas de clases, secuencia y actividad con edición textual, rápido y sin distracciones. Stand-alone 14.2.",
   "tags": [
    "uml",
    "diagramas",
    "java",
    "open-source",
    "eclipse"
   ],
   "rel": [
    "balsamiq-mockups",
    "eclipse"
   ],
   "succ": null
  },
  {
   "slug": "gel",
   "name": "GEL",
   "year": 2002,
   "until": 2004,
   "cat": "editor",
   "sub": "ides",
   "ctx": "IDE de Java ligero y compilado nativamente para Windows, freeware de GExperts (creadores de GExperts para Delphi). Alternativa al JDK puro con autocompletado, ayuda de parámetros y gestión de proyectos.",
   "tags": [
    "ide",
    "java",
    "windows",
    "freeware",
    "gexperts"
   ],
   "rel": [
    "netbeans",
    "eclipse",
    "intellij-idea"
   ],
   "succ": "netbeans"
  },
  {
   "slug": "visual-basic-net",
   "name": "Visual Basic .NET",
   "year": 2002,
   "until": null,
   "cat": "lenguaje",
   "sub": "basic",
   "ctx": "El heredero de VB6 en el mundo .NET. Mismo sabor de BASIC, pero sobre una plataforma moderna de verdad.",
   "tags": [
    "vbnet",
    "microsoft",
    "dotnet",
    "ide"
   ],
   "rel": [
    "visual-basic-6",
    "visual-studio"
   ],
   "succ": null
  },
  {
   "slug": "visual-studio",
   "name": "Visual Studio",
   "year": 2002,
   "until": 2010,
   "cat": "editor",
   "sub": "ides",
   "ctx": "El IDE definitivo para desarrollo Microsoft. Cuando tuve que trabajar con C# y .NET, Visual Studio fue la única opción seria. Potente pero pesado.",
   "tags": [
    "ide",
    "microsoft",
    "dotnet",
    "c-sharp",
    "windows"
   ],
   "rel": [
    "visual-basic-6",
    "eclipse",
    "visual-basic-net"
   ],
   "succ": "visual-studio-code"
  },
  {
   "slug": "mozilla-firefox",
   "name": "Mozilla Firefox",
   "year": 2004,
   "until": 2018,
   "cat": "tool",
   "sub": "navegadores",
   "ctx": "El navegador que rescató la web del monopolio de Internet Explorer. Extensiones, tabs, y el espíritu open source. Mi navegador principal durante más de una década.",
   "tags": [
    "browser",
    "mozilla",
    "firefox",
    "web",
    "open-source"
   ],
   "rel": [
    "netscape-navigator",
    "internet-explorer",
    "google-chrome"
   ],
   "succ": "google-chrome"
  },
  {
   "slug": "acronis-true-image",
   "name": "Acronis True Image",
   "year": 2005,
   "until": null,
   "cat": "tool",
   "sub": "archivos-y-respaldo",
   "ctx": "El heredero de Norton Ghost en mis rutinas de respaldo. Imágenes de disco y restauración confiable, con interfaz moderna.",
   "tags": [
    "backup",
    "imaging",
    "acronis",
    "disaster-recovery"
   ],
   "rel": [
    "norton-ghost",
    "vmware-workstation"
   ],
   "succ": null
  },
  {
   "slug": "oracle-sql-developer",
   "name": "Oracle SQL Developer",
   "year": 2006,
   "until": 2018,
   "cat": "database",
   "sub": null,
   "ctx": "La herramienta oficial de Oracle para trabajar con sus bases de datos. Gratuita, completa, y el estándar durante mis años de docencia Oracle. Reemplazó a SQL*Plus para desarrollo.",
   "tags": [
    "oracle",
    "database",
    "sql",
    "plsql",
    "ide"
   ],
   "rel": [
    "netbeans",
    "oracle-database",
    "datagrip"
   ],
   "succ": "datagrip"
  },
  {
   "slug": "intellij-idea",
   "name": "IntelliJ IDEA",
   "year": 2007,
   "until": null,
   "cat": "editor",
   "sub": "ides",
   "ctx": "El IDE que me hizo dejar Eclipse después de 8 años. Cuando probé el refactoring automático supe que no volvería atrás.",
   "tags": [
    "ide",
    "java",
    "jetbrains",
    "refactoring",
    "productivity"
   ],
   "rel": [
    "gel",
    "eclipse",
    "netbeans",
    "visual-studio-code",
    "datagrip"
   ],
   "succ": null
  },
  {
   "slug": "sublime-text",
   "name": "Sublime Text",
   "year": 2012,
   "until": null,
   "cat": "editor",
   "sub": "editores-de-codigo",
   "ctx": "El editor que demostró que la velocidad vuelve a importar. Antes de VS Code, fue el estándar de facto de los editores modernos.",
   "tags": [
    "editor",
    "sublime",
    "proprietary",
    "fast"
   ],
   "rel": [
    "visual-studio-code",
    "atom"
   ],
   "succ": null
  },
  {
   "slug": "docker",
   "name": "Docker",
   "year": 2013,
   "until": null,
   "cat": "infra",
   "sub": null,
   "ctx": "La revolución de los contenedores. Empaquetar aplicaciones con todas sus dependencias y desplegarlas en cualquier lado. Cambió mi forma de desarrollar y desplegar software.",
   "tags": [
    "docker",
    "containers",
    "devops",
    "microservices",
    "virtualization"
   ],
   "rel": [
    "vmware-workstation",
    "kubernetes",
    "virtualbox"
   ],
   "succ": null
  },
  {
   "slug": "thebrain-8",
   "name": "TheBrain 8",
   "year": 2013,
   "until": null,
   "cat": "tool",
   "sub": "diagramacion",
   "ctx": "La octava generación del brain de TheBrain Technologies: búsqueda inteligente, nueva interfaz y servicios en la nube. Instalador 8.0.1.3.",
   "tags": [
    "mapas-mentales",
    "conocimiento",
    "bases-de-conocimiento",
    "windows"
   ],
   "rel": [
    "personal-brain",
    "mindmanager",
    "axon-idea-processor"
   ],
   "succ": null
  },
  {
   "slug": "atom",
   "name": "Atom",
   "year": 2014,
   "until": null,
   "cat": "editor",
   "sub": "editores-de-codigo",
   "ctx": "El editor hackeable de GitHub. Intentó ser todo para todos, con tecnologías web dentro de un editor.",
   "tags": [
    "editor",
    "github",
    "electron",
    "open-source"
   ],
   "rel": [
    "visual-studio-code",
    "sublime-text"
   ],
   "succ": null
  },
  {
   "slug": "kubernetes",
   "name": "Kubernetes",
   "year": 2015,
   "until": null,
   "cat": "infra",
   "sub": null,
   "ctx": "El estándar de orquestación de contenedores. Cuando Docker resolvió el empaquetado, Kubernetes resolvió el despliegue a escala. Cambió cómo pienso la infraestructura.",
   "tags": [
    "kubernetes",
    "k8s",
    "containers",
    "orchestration",
    "devops"
   ],
   "rel": [
    "docker",
    "vmware-workstation"
   ],
   "succ": null
  },
  {
   "slug": "virtualbox",
   "name": "Oracle VM VirtualBox",
   "year": 2015,
   "until": null,
   "cat": "infra",
   "sub": null,
   "ctx": "La virtualización que sobrevive a todo. Cuando VMware se volvió corporativo, VirtualBox siguió siendo el laboratorio gratuito en cualquier máquina.",
   "tags": [
    "virtualization",
    "oracle",
    "vm",
    "x86"
   ],
   "rel": [
    "vmware-workstation",
    "docker"
   ],
   "succ": null
  },
  {
   "slug": "visual-studio-code",
   "name": "Visual Studio Code",
   "year": 2015,
   "until": null,
   "cat": "editor",
   "sub": "editores-de-codigo",
   "ctx": "El editor que conquistó al mundo. Ligero, extensible, y gratuito. Pasó de ser el nuevo chico a ser el estándar de facto en pocos años. Mi editor principal desde entonces.",
   "tags": [
    "vscode",
    "microsoft",
    "editor",
    "open-source",
    "electron"
   ],
   "rel": [
    "intellij-idea",
    "sublime-text",
    "atom"
   ],
   "succ": null
  },
  {
   "slug": "balsamiq-mockups",
   "name": "Balsamiq Mockups",
   "year": 2017,
   "until": null,
   "cat": "tool",
   "sub": "diagramacion",
   "ctx": "Wireframes de baja fidelidad con estética de boceto dibujado a mano, hoy «Balsamiq Wireframes». Se conserva el instalador de escritorio 3.5.14.",
   "tags": [
    "wireframes",
    "prototipado",
    "ui",
    "diseño",
    "windows"
   ],
   "rel": [
    "umlet"
   ],
   "succ": null
  },
  {
   "slug": "datagrip",
   "name": "DataGrip",
   "year": 2018,
   "until": null,
   "cat": "database",
   "sub": null,
   "ctx": "El IDE de bases de datos de JetBrains. Consistencia total con IntelliJ y refactorización de SQL de verdad.",
   "tags": [
    "database",
    "jetbrains",
    "sql",
    "ide"
   ],
   "rel": [
    "oracle-sql-developer",
    "intellij-idea",
    "oracle-database"
   ],
   "succ": null
  },
  {
   "slug": "google-chrome",
   "name": "Google Chrome",
   "year": 2018,
   "until": null,
   "cat": "tool",
   "sub": "navegadores",
   "ctx": "El navegador de la madurez. Cuando Firefox cedió terreno, Chrome ya era la infraestructura silenciosa de la web.",
   "tags": [
    "browser",
    "google",
    "internet",
    "web"
   ],
   "rel": [
    "mozilla-firefox",
    "internet-explorer"
   ],
   "succ": null
  },
  {
   "slug": "openclaw",
   "name": "OpenClaw",
   "year": 2025,
   "until": 2026,
   "cat": "tool",
   "sub": "agentes-ia",
   "ctx": "Asistente personal de IA open source: corre en tu propia máquina y usa tus apps de mensajería como interfaz. Lo usé desde su publicación hasta el 22 de abril de 2026.",
   "tags": [
    "ai",
    "agentes",
    "open-source",
    "asistente-personal",
    "clawdbot"
   ],
   "rel": [],
   "succ": null
  }
 ]
};
