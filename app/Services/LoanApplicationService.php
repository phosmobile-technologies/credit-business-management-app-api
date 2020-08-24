<?php

namespace App\Services;


use App\Models\enums\LoanDocumentOwnerType;
use App\Models\LoanApplication;
use App\Repositories\Interfaces\LoanApplicationRepositoryInterface;
use App\Repositories\Interfaces\LoanDocumentRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LoanApplicationService
{
    /**
     * @var LoanApplicationRepositoryInterface
     */
    private $loanApplicationRepository;
    /**
     * @var LoanDocumentRepositoryInterface
     */
    private $loanDocumentRepository;

    /**
     * LoanApplicationService constructor.
     * @param LoanApplicationRepositoryInterface $loanApplicationRepository
     * @param LoanDocumentRepositoryInterface    $loanDocumentRepository
     */
    public function __construct(LoanApplicationRepositoryInterface $loanApplicationRepository, LoanDocumentRepositoryInterface $loanDocumentRepository)
    {
        $this->loanApplicationRepository = $loanApplicationRepository;
        $this->loanDocumentRepository    = $loanDocumentRepository;
    }

    /**
     * Create a loan application.
     *
     * @param array $loanApplicationData
     * @return \App\Models\LoanApplication
     */
    public function createLoanApplication(array $loanApplicationData)
    {
        $loanApplication = $this->loanApplicationRepository->create($loanApplicationData);

        $loanFiles = isset($loanApplicationData['loan_files']) ? $loanApplicationData['loan_files'] : null;

        if ($loanFiles) {
            foreach ($loanFiles as $loanFile) {
                $this->saveLoanDocumentForLoanApplication($loanFile, $loanApplication);
            }

        }

        return $loanApplication;
    }

    /**
     * @param string $loan_application_id
     * @param string $admin_staff_id
     * @param string $branch_manager_id
     * @return \App\Models\LoanApplication
     */
    public function assignLoanApplicationToAdminStaff(string $loan_application_id, string $admin_staff_id, string $branch_manager_id): LoanApplication
    {
        return $this->loanApplicationRepository->assign($loan_application_id, $admin_staff_id, $branch_manager_id);
    }

    /**
     * @param string      $loan_application_id
     * @param string      $status
     * @param null|string $message
     * @return LoanApplication
     */
    public function processLoanApplication(string $loan_application_id, string $status, ?string $message): LoanApplication
    {
        return $this->loanApplicationRepository->process($loan_application_id, $status, $message);
    }

    /**
     * Save a loan document for a loan application.
     *
     * @param UploadedFile    $loanDocument
     * @param LoanApplication $loanApplication
     * @return \App\Models\LoanDocument
     */
    public function saveLoanDocumentForLoanApplication(UploadedFile $loanDocument, LoanApplication $loanApplication)
    {
        $loanDocumentPath = Storage::disk('local')->put('loan-documents', $loanDocument);
        return $this->loanDocumentRepository->create([
            'owner_id'   => $loanApplication->id,
            'owner_type' => LoanDocumentOwnerType::LOAN_APPLICATION,
            'url'        => $loanDocumentPath
        ]);
    }
}
