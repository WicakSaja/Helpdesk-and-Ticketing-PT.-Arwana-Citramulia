<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $masterAdmin;
    protected $helpdesk;
    protected $technician;
    protected $requester;

    protected function setUp(): void
    {
        parent::setUp();

        // Create departments
        $department = Department::factory()->create();

        // Create users with roles
        $this->masterAdmin = User::factory()->create(['department_id' => $department->id]);
        $this->masterAdmin->assignRole('master-admin');

        $this->helpdesk = User::factory()->create(['department_id' => $department->id]);
        $this->helpdesk->assignRole('helpdesk');

        $this->technician = User::factory()->create(['department_id' => $department->id]);
        $this->technician->assignRole('technician');

        $this->requester = User::factory()->create(['department_id' => $department->id]);
        $this->requester->assignRole('requester');

        // Create some tickets
        $category = Category::factory()->create();
        $status = TicketStatus::firstOrCreate(['name' => 'OPEN']);

        Ticket::factory()
            ->count(5)
            ->create([
                'requester_id' => $this->requester->id,
                'status_id' => $status->id,
                'category_id' => $category->id,
            ]);
    }

    /**
     * Test export endpoint unauthorized (unauthenticated)
     */
    public function test_export_unauthorized()
    {
        $response = $this->getJson('/api/export?type=all-tickets');

        $response->assertStatus(401);
    }

    /**
     * Test export endpoint forbidden (insufficient role)
     */
    public function test_export_forbidden()
    {
        $response = $this->actingAs($this->requester)
            ->getJson('/api/export?type=all-tickets');

        $response->assertStatus(403);
    }

    /**
     * Test export without required type parameter
     */
    public function test_export_missing_required_parameter()
    {
        $response = $this->actingAs($this->masterAdmin)
            ->getJson('/api/export');

        $response->assertStatus(422)
            ->assertJsonValidationErrors('type');
    }

    /**
     * Test export with invalid type parameter
     */
    public function test_export_invalid_type()
    {
        $response = $this->actingAs($this->masterAdmin)
            ->getJson('/api/export?type=invalid-type');

        $response->assertStatus(422)
            ->assertJsonValidationErrors('type');
    }

    /**
     * Test export all-tickets by master-admin
     */
    public function test_export_all_tickets_by_master_admin()
    {
        $response = $this->actingAs($this->masterAdmin)
            ->getJson('/api/export?type=all-tickets');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->assertHeader('Content-Disposition');
    }

    /**
     * Test export all-tickets by helpdesk
     */
    public function test_export_all_tickets_by_helpdesk()
    {
        $response = $this->actingAs($this->helpdesk)
            ->getJson('/api/export?type=all-tickets');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test export by-status
     */
    public function test_export_by_status()
    {
        $response = $this->actingAs($this->masterAdmin)
            ->getJson('/api/export?type=by-status&status=OPEN');

        $response->assertStatus(200);
    }

    /**
     * Test export with date range filter
     */
    public function test_export_with_date_range()
    {
        $response = $this->actingAs($this->masterAdmin)
            ->getJson('/api/export?type=all-tickets&start_date=2026-01-01&end_date=2026-01-31');

        $response->assertStatus(200);
    }

    /**
     * Test export with invalid date range
     */
    public function test_export_invalid_date_range()
    {
        $response = $this->actingAs($this->masterAdmin)
            ->getJson('/api/export?type=all-tickets&start_date=2026-01-31&end_date=2026-01-01');

        $response->assertStatus(422)
            ->assertJsonValidationErrors('end_date');
    }

    /**
     * Test export by-technician
     */
    public function test_export_by_technician()
    {
        $response = $this->actingAs($this->masterAdmin)
            ->getJson('/api/export?type=by-technician');

        $response->assertStatus(200);
    }

    /**
     * Test export by-department
     */
    public function test_export_by_department()
    {
        $response = $this->actingAs($this->masterAdmin)
            ->getJson('/api/export?type=by-department');

        $response->assertStatus(200);
    }

    /**
     * Test export filename format
     */
    public function test_export_filename_format()
    {
        $response = $this->actingAs($this->masterAdmin)
            ->getJson('/api/export?type=all-tickets');

        $contentDisposition = $response->header('Content-Disposition');
        $this->assertStringContainsString('Laporan_semua-ticket_', $contentDisposition);
        $this->assertStringContainsString('.xlsx', $contentDisposition);
    }

    /**
     * Test export with department filter
     */
    public function test_export_with_department_filter()
    {
        $response = $this->actingAs($this->masterAdmin)
            ->getJson('/api/export?type=all-tickets&department_id=1');

        $response->assertStatus(200);
    }

    /**
     * Test export with technician filter
     */
    public function test_export_with_technician_filter()
    {
        $response = $this->actingAs($this->masterAdmin)
            ->getJson('/api/export?type=all-tickets&technician_id=1');

        $response->assertStatus(200);
    }
}
