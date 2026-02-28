<?php

class Order extends Model
{
    protected $table = 'orders';

    public function create($data)
    {
        $query = "INSERT INTO " . $this->table . " (client_id, type, capacity, quantity, status) VALUES (:client_id, :type, :capacity, :quantity, :status)";
        $stmt = $this->db->prepare($query);

        $status = isset($data['status']) ? $data['status'] : 'pending';

        $stmt->bindParam(':client_id', $data['client_id']);
        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':capacity', $data['capacity']);
        $stmt->bindParam(':quantity', $data['quantity']);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function updateStatus($id, $status)
    {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function findByClientId($clientId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE client_id = :client_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':client_id', $clientId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
