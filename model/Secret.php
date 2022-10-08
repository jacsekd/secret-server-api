<?php

/**
 * Class Secret
 *
 * This class is the model for Secret.
 */
class Secret
{
    /**
     * @var string $hash contains the hash, that is generated randomly, used as primary key in the database
     */
    private string $hash;

    /**
     * @var string $secretText is the text, that needs to be stored
     */
    private string $secretText;

    /**
     * @var string $createdAt is a date, when the secret was created
     */
    private string $createdAt;

    /**
     * @var string $expiresAt is a date, when the secret expires. If the value equals with $createdAt value, the secret never expires
     */
    private string $expiresAt;

    /**
     * @var int $remainingViews is a counter, that decreases everytime the secret is read. If it is 0, the secret is not retrievable anymore
     */
    private int $remainingViews;

    /**
     * @var PDO
     */
    private PDO $conn;

    /**
     * @var string $table is the name of the table in the database that contains the secrets
     */
    private string $table = 'secret';

    /**
     * @var string $xmlString can contain the XML representation of the secret
     */
    private string $xmlString;

    /**
     * @var string $jsonString can contain the JSON representation of the secret
     */
    private string $jsonString;

    /**
     * Secret constructor.
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getSecretText(): string
    {
        return $this->secretText;
    }

    /**
     * @param string $secretText
     */
    public function setSecretText(string $secretText): void
    {
        $this->secretText = $secretText;
    }

    /**
     * @return string The returned value is formatted.
     * @throws Exception Emits Exception in case of an error.
     */
    public function getCreatedAt(): string
    {
        $date = new DateTime($this->createdAt);
        return $date->format('Y-m-d\TH:i:s.v\Z');
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string The returned value is formatted.
     * @throws Exception Emits Exception in case of an error.
     */
    public function getExpiresAt(): string
    {
        $date = new DateTime($this->expiresAt);
        return $date->format('Y-m-d\TH:i:s.v\Z');
    }

    /**
     * @param string $expiresAt
     */
    public function setExpiresAt(string $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return int
     */
    public function getRemainingViews(): int
    {
        return $this->remainingViews;
    }

    /**
     * @param int $remainingViews
     */
    public function setRemainingViews(int $remainingViews): void
    {
        $this->remainingViews = $remainingViews;
    }

    /**
     * @return string
     */
    public function getXmlString(): string
    {
        return $this->xmlString;
    }

    /**
     * @return string
     */
    public function getJsonString(): string
    {
        return $this->jsonString;
    }

    /**
     * Reads one secret.
     *
     * The function tries to get a secret from the database using the object's hash attribute. The secret is only retrievable, if it's remainingViews is greater than 0 and it didn't expire.
     * After fetching the data and setting the object's attributes, the secret's remainingViews counter is decreased by 1.
     *
     * @return bool True, if both retrieving the secret and decreasing it's remainingViews attribute is successful. False if one of them fails.
     */
    public function readOne(): bool
    {
        $query = "SELECT secretText, createdAt, expiresAt, remainingViews from " . $this->table . " WHERE hash = :hash AND remainingViews > 0 AND (createdAt = expiresAt OR expiresAt > now())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hash', $this->hash);
        if (!$stmt->execute() || $stmt->rowCount() != 1) return false;
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->secretText = $row["secretText"];
        $this->createdAt = $row["createdAt"];
        $this->expiresAt = $row["expiresAt"];
        $this->remainingViews = $row["remainingViews"] - 1;

        $query = "UPDATE " . $this->table . " SET remainingViews = remainingViews - 1 WHERE hash = :hash";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hash', $this->hash);
        if (!$stmt->execute()) return false;
        return true;
    }

    /**
     * Saves a new secret.
     *
     * The function inserts a new record into the database, using the object's attributes.
     *
     * @return bool True, if inserting a new record into the database was successful, false otherwise.
     */
    public function saveNew(): bool
    {
        $query = "INSERT INTO " . $this->table . " (hash, secretText, createdAt, expiresAt, remainingViews) VALUES (:hash, :secretText, :createdAt, :expiresAt, :remainingViews)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':hash', $this->hash);
        $stmt->bindParam(':secretText', $this->secretText);
        $stmt->bindParam(':createdAt', $this->createdAt);
        $stmt->bindParam(':expiresAt', $this->expiresAt);
        $stmt->bindParam(':remainingViews', $this->remainingViews);
        return $stmt->execute();
    }

    /**
     * Checks, if the hash already exists in the database.
     *
     * @return bool True, if the object's hash is already in the database, false otherwise.
     */
    private function isHashAlreadyExists(): bool
    {
        $query = "SELECT hash from " . $this->table . " WHERE hash = :hash ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hash', $this->hash);
        $stmt->execute();
        return $stmt->rowCount() != 0;
    }

    /**
     * The function generates a random hash for the object.
     * If a hash was generated that is already in use, it generates a new.
     * It sets the objects hash attribute.
     *
     * @throws Exception if it was not possible to gather sufficient entropy for random_bytes().
     */
    public function generateHash()
    {
        do {
            $this->hash = bin2hex(random_bytes(32));
        } while ($this->isHashAlreadyExists());
    }

    /**
     * The function returns an associative array that contains the needed attributes of the object.
     *
     * @return array Returns an associative array that contains the needed attributes of the object.
     * @throws Exception Emits Exception in case of an error.
     */
    public function toArray(): array
    {
        return [
            'hash' => $this->getHash(),
            'secretText' => $this->getSecretText(),
            'createdAt' => $this->getCreatedAt(),
            'expiresAt' => $this->getExpiresAt(),
            'remainingViews' => $this->getRemainingViews()
        ];
    }

    /**
     * The function creates an XML formatted string.
     * It uses the array returned by toArray()
     * It sets the object's xmlString attribute
     *
     * @return string Returns a string that is XML formatted of the object.
     * @throws Exception Emits Exception in case of an error.
     */
    public function toXML(): string
    {
        $this->xmlString = '<?xml version="1.0" encoding="UTF-8"?><Secret>';
        foreach ($this->toArray() as $key => $value) {
            $this->xmlString .= '<' . $key . '>' . $value . '</' . $key . '>';
        }
        $this->xmlString .= '</Secret>';
        return $this->xmlString;
    }

    /**
     * The function creates an JSON formatted string.
     * It uses the array returned by toArray()
     * It sets the object's jsonString attribute
     *
     * @return string Returns a string that is JSON formatted of the object.
     * @throws Exception Emits Exception in case of an error.
     */
    public function toJSON(): string
    {
        $this->jsonString = json_encode($this->toArray());
        return $this->jsonString;
    }

    //public function toYAML(): string
    //{
    //   return '';
    //}

}
