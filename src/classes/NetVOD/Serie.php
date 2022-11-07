<?php
namespace iutnc\NetVOD\NetVOD;
class Serie
{
    protected string $titre;
    protected string $cheminImage;
    protected string $descriptif;
    protected int $dateSortie;
    protected string $dateAjout;
    protected array $listeEpisode;

    /**
     * @param string $titre
     * @param string $descriptif
     * @param int $dateSortie
     * @param string $dateAjout
     * @param array $listeEpisode
     */
    public function __construct(string $titre, string $genre, string $public, string $descriptif, int $dateSortie, string $dateAjout, array $listeEpisode)
    {
        $this->titre = $titre;
        $this->descriptif = $descriptif;
        $this->dateSortie = $dateSortie;
        $this->dateAjout = $dateAjout;
        $this->listeEpisode = $listeEpisode;
    }


    public function __get(string $name)
    {
        if (property_exists($this, $name)) return $this->$name;
        throw new InvalidPropertyNameException("Le nom est invalide");
    }

    /**
     * @throws InvalidPropertyNameException
     * @throws NonEditablePropertyException
     * @throws InvalidPropertyValueException
     */
    public function __set(string $name, $value): void
    {
        if(property_exists($this, $name)) {
            if($name === "fichierAudio" || $name === "titre") throw new NonEditablePropertyException("$name ne peut etre modifier");
            if($name === "duree" && $value < 0) throw new InvalidPropertyValueException("La duree ne peut etre negative");
            $this->$name = $value;
        } else throw new InvalidPropertyNameException("Le nom est invalide");
    }

    /** insert une serie dans la BDD
     * @return void
     */
    public function insertSerie()
    {
        ConnectionFactory::setConfig("dbData.ini");
        $db = ConnectionFactory::makeConnection();
        $query = $db->prepare("insert into serie(titre, genre, descriptif,image, annee,date_ajout) values (?, ?,?, ?, ?, ?)");
        $query->bindParam(1,$this->titre);
        $query->bindParam(2, $this->genre);
        $query->bindParam(3,$this->duree);
        $query->bindParam(4,$this->fichierAudio);
        $query->bindParam(5,$this->auteur);
        $query->execute();
        $query->closeCursor();
        $db->commit();
    }



}