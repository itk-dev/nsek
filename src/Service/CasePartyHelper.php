<?php

namespace App\Service;

use App\Entity\CaseEntity;
use App\Entity\ResidentComplaintBoardCase;
use App\Repository\CasePartyRelationRepository;

class CasePartyHelper
{
    /**
     * @var CasePartyRelationRepository
     */
    private $relationRepository;

    public function __construct(CasePartyRelationRepository $relationRepository)
    {
        $this->relationRepository = $relationRepository;
    }

    /**
     * Returns array containing relevant party arrays.
     *
     * @param CaseEntity $case
     * @return array[]
     */
    public function getRelevantPartiesByCase(CaseEntity $case): array
    {
        switch (get_class($case)){
            case ResidentComplaintBoardCase::class:
                // Get relations from both sides
                $complainantRelations = $this->relationRepository
                    ->findBy(['case' => $case, 'type' => ['Tenant', 'Representative'], 'softDeleted' => false]);
                $counterpartyRelations = $this->relationRepository
                    ->findBy(['case' => $case, 'type' => ['Landlord', 'Administrator'], 'softDeleted' => false]);
                break;
        }

        // Make them into arrays
        $complainants = [];
        $counterparties = [];

        foreach ($complainantRelations as $relation) {
            array_push($complainants, $relation->getParty());
        }

        foreach ($counterpartyRelations as $relation) {
            array_push($counterparties, $relation->getParty());
        }

        return array('complainants' => $complainants, 'counterparties' => $counterparties);
    }
}