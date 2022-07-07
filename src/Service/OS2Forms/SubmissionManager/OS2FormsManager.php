<?php

namespace App\Service\OS2Forms\SubmissionManager;

use App\Service\CaseManager;

class OS2FormsManager
{
    public function __construct(private CaseManager $caseManager, private ResidentComplaintBoardCaseTypeManager $residentComplaintBoardCaseTypeManager)
    {
    }

    /**
     * Forwards handling of OS2Form submission and provides a submission manager based on webform id.
     */
    public function handleOS2FormsSubmission(string $webformId, string $sender, array $submissionData)
    {
        // TODO: Needs updating when form ids are finalized and hearing response forms are added.
        match ($webformId) {
            'tvist1_opret_sag_test' => $this->caseManager->handleOS2FormsCaseSubmission($sender, $submissionData, $this->residentComplaintBoardCaseTypeManager),
        };
    }
}