<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Legal extends Model
{
    use HasFactory;

    protected $fillable = [
        'legal_id',
        'title',
        'authority',
        'relevant_clause',
        'reference_others',
        'category',
        'effective_date',
        'relevant',
        'description',
        'what_affected',
        'action_required',
        'responsible_person',
        'last_review_date',
        'review_frequency',
        'further_action_bool',
        'further_action',
        'compliance_status',
        'evidence_compliance',
        'remarks',
        'status_approval'
    ];

    protected $casts = [
        'effective_date' => 'date',
        'last_review_date' => 'date',
    ];

    /**
     * Generate unique legal ID
     */
    public static function generateLegalId()
    {
        $lastLegal = self::orderBy('id', 'desc')->first();

        if (!$lastLegal) {
            return 'LR-ENMS-001';
        }

        // Extract the numeric part from the last legal_id
        preg_match('/(\d+)$/', $lastLegal->legal_id, $matches);
        $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
        $nextNumber = $lastNumber + 1;

        // Determine prefix based on category
        $prefix = 'LR-ENMS-';
        if (stripos($lastLegal->title, 'EECA') !== false || stripos($lastLegal->title, 'Energy Efficiency') !== false) {
            $prefix = 'LR-EECA-';
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get formatted effective date
     */
    public function getFormattedEffectiveDateAttribute()
    {
        return $this->effective_date ? $this->effective_date->format('Y-m-d') : 'N/A';
    }

    /**
     * Get formatted last review date
     */
    public function getFormattedLastReviewDateAttribute()
    {
        return $this->last_review_date ? $this->last_review_date->format('Y-m-d') : '-';
    }

    /**
     * Check if legal document is relevant
     */
    public function getIsRelevantAttribute()
    {
        return $this->relevant === 'Y';
    }

    /**
     * Check if legal document is compliant
     */
    public function getIsCompliantAttribute()
    {
        return $this->compliance_status === 'Compliant';
    }

    /**
     * Get next review date based on frequency
     */
    public function getNextReviewDateAttribute()
    {
        if (!$this->last_review_date) {
            return null;
        }

        $lastReview = Carbon::parse($this->last_review_date);

        switch ($this->review_frequency) {
            case 'Monthly':
                return $lastReview->addMonth();
            case 'Quarterly':
                return $lastReview->addMonths(3);
            case 'Bi-annually':
                return $lastReview->addMonths(6);
            case 'Annually':
            default:
                return $lastReview->addYear();
        }
    }

    /**
     * Check if review is due
     */
    public function getIsReviewDueAttribute()
    {
        $nextReviewDate = $this->next_review_date;
        return $nextReviewDate ? Carbon::now()->gte($nextReviewDate) : false;
    }

    // Tambahkan method ini ke dalam Legal model yang sudah ada

/**
 * Relationship with Legal Items
 */
public function legalItems()
{
    return $this->hasMany(LegalItem::class);
}

/**
 * Get active legal items
 */
public function activeLegalItems()
{
    return $this->hasMany(LegalItem::class)->active();
}


    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->compliance_status) {
            case 'Compliant':
                return 'badge bg-success';
            case 'In Progress':
                return 'badge bg-warning';
            case 'Non-Compliant':
                return 'badge bg-danger';
            case 'Not Applicable':
                return 'badge bg-secondary';
            default:
                return 'badge bg-light text-dark';
        }
    }

    /**
     * Scope for relevant documents
     */
    public function scopeRelevant($query)
    {
        return $query->where('relevant', 'Y');
    }

    /**
     * Scope for specific category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for specific compliance status
     */
    public function scopeByComplianceStatus($query, $status)
    {
        return $query->where('compliance_status', $status);
    }

    /**
     * Scope for documents requiring further action
     */
    public function scopeRequiresFurtherAction($query)
    {
        return $query->where('further_action_bool', 'Yes');
    }

    /**
     * Scope for review due documents
     */
    public function scopeReviewDue($query)
    {
        $today = Carbon::now();

        return $query->where(function($q) use ($today) {
            $q->where('review_frequency', 'Monthly')
              ->whereDate('last_review_date', '<=', $today->copy()->subMonth())
              ->orWhere('review_frequency', 'Quarterly')
              ->whereDate('last_review_date', '<=', $today->copy()->subMonths(3))
              ->orWhere('review_frequency', 'Bi-annually')
              ->whereDate('last_review_date', '<=', $today->copy()->subMonths(6))
              ->orWhere('review_frequency', 'Annually')
              ->whereDate('last_review_date', '<=', $today->copy()->subYear());
        });
    }
}
