<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlockRepository")
 */
class Block
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint")
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $previous;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $witness;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $transactionMerkleRoot;

    /**
     * @ORM\Column(type="integer")
     */
    private $transactionsCount;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    // private $transactions;

    public function getId()
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getPrevious(): ?string
    {
        return $this->previous;
    }

    public function setPrevious(string $previous): self
    {
        $this->previous = $previous;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getWitness(): ?string
    {
        return $this->witness;
    }

    public function setWitness(string $witness): self
    {
        $this->witness = $witness;

        return $this;
    }

    public function getTransactionMerkleRoot(): ?string
    {
        return $this->transactionMerkleRoot;
    }

    public function setTransactionMerkleRoot(?string $transactionMerkleRoot): self
    {
        $this->transactionMerkleRoot = $transactionMerkleRoot;

        return $this;
    }

    public function getTransactionsCount(): ?int
    {
        return $this->transactionsCount;
    }

    public function setTransactionsCount(int $transactionsCount): self
    {
        $this->transactionsCount = $transactionsCount;

        return $this;
    }

    public function getTransactions()
    {
        return $this->transactions;
    }

    public function setTransactions($transactions): self
    {
        $this->transactions = $transactions;

        return $this;
    }
}
