<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifeCycleCallbacks(     )
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan("today", message="La date ne peut pas être inférieur à aujourd'hui")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Expression(
     *      "this.getStartDate() < this.getEndDate()",
     *      message="La date de fin ne doit pas être antérieure à la date de début")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    
    /**
     * Callback à chaque fois que l'on crée une réservation
     * 
     * @ORM\PrePersist
     *
     * @return void
     */
    public function prePersist(){  // ->Calcul de la date de création et du montant total
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }

        if (empty($this->amount)) {
            # prix de l'annonce * nombre de jour
           $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    public function getDuration(){ // -> Calcul du montant
        $diff = $this->endDate->diff($this->startDate); 
        return $diff->days;
    }

    public function isBookableDates(){ // -> Véerifier si la réservation est disponible (s'il n'y a pas d'autre réservation en cours)
        // 1) Il faut connaitre les dates qui sont impossibles pour l'annonce
        $notAvailableDays = $this->ad->getNotAvailableDays();
        // 2) Il faut comparer les dates choisies et les dates indisponibles
        $bookingsDays = $this->getDays();


        // Tableau des chaines de caractères de mes journées
        $formatDay    = function($day){
            return $day->format('Y-m-d');
        };

            // Tous les jours que l'on a réservé
        $days         = array_map($formatDay, $bookingsDays);

            // Tous les jours non disponibles de l'annonce
        $notAvailable = array_map($formatDay, $notAvailableDays);

        // On boucle sur les dates de réservation de notre annonce pour voir si elles sont disponibles
        foreach($days as $day){
            if(array_search($day, $notAvailable) !== false) return false;
        }

        return true;
    }

    /**
     * Permet de récupérer un tableau des journées qui correspondent à ma réservation
     *
     * @return array Un tableau d'objets DateTime représentant les jours de la réservation
     */
    public function getDays(){
        $result = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24 * 60 * 60   // Convertir une journée en milliseconde (24h * 60 minutes dans une heure * 60 secondes dans une minute)
        );

        $days = array_map(function($dayTimestamp){
            return new \DateTime(date('Y-m-d', $dayTimestamp));
        }, $result);

        return $days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
