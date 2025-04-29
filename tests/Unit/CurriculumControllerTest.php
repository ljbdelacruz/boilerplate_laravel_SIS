<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Curriculum;
use App\Models\Section;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $schoolYear;
    protected $section;
    protected $course;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->user = User::factory()->create();
        $this->schoolYear = SchoolYear::factory()->create(['is_active' => true]);
        $this->section = Section::factory()->create(['school_year_id' => $this->schoolYear->id]);
        $this->course = Course::factory()->create();
    }

    public function test_store_curriculum()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('curriculums.store'), [
            'section_id' => $this->section->id,
            'subject_id' => $this->course->id,
            'time' => '08:00 AM - 09:00 AM',
        ]);

        $response->assertRedirect(route('curriculums.index'));
        $response->assertSessionHas('success', 'Curriculum added successfully!');

        $this->assertDatabaseHas('curriculums', [
            'section_id' => $this->section->id,
            'subject_id' => $this->course->id,
            'time' => '08:00 AM - 09:00 AM',
        ]);
    }

    public function test_update_curriculum()
    {
        $this->actingAs($this->user);

        $curriculum = Curriculum::factory()->create([
            'section_id' => $this->section->id,
            'subject_id' => $this->course->id,
            'time' => '08:00 AM - 09:00 AM',
        ]);

        $response = $this->put(route('curriculums.update', $curriculum), [
            'section_id' => $this->section->id,
            'subject_id' => $this->course->id,
            'time' => '09:00 AM - 10:00 AM',
        ]);

        $response->assertRedirect(route('curriculums.index'));
        $response->assertSessionHas('success', 'Curriculum updated successfully!');

        $this->assertDatabaseHas('curriculums', [
            'id' => $curriculum->id,
            'section_id' => $this->section->id,
            'subject_id' => $this->course->id,
            'time' => '09:00 AM - 10:00 AM',
        ]);
    }

    public function test_delete_curriculum()
    {
        $this->actingAs($this->user);

        $curriculum = Curriculum::factory()->create([
            'section_id' => $this->section->id,
            'subject_id' => $this->course->id,
            'time' => '08:00 AM - 09:00 AM',
        ]);

        $response = $this->delete(route('curriculums.destroy', $curriculum));

        $response->assertRedirect(route('curriculums.index'));
        $response->assertSessionHas('success', 'Curriculum deleted successfully!');

        $this->assertDatabaseMissing('curriculums', ['id' => $curriculum->id]);
    }

    public function test_store_curriculum_validation()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('curriculums.store'), []);

        $response->assertSessionHasErrors(['section_id', 'subject_id', 'time']);
    }

    public function test_update_curriculum_validation()
    {
        $this->actingAs($this->user);

        $curriculum = Curriculum::factory()->create([
            'section_id' => $this->section->id,
            'subject_id' => $this->course->id,
            'time' => '08:00 AM - 09:00 AM',
        ]);

        $response = $this->put(route('curriculums.update', $curriculum), []);

        $response->assertSessionHasErrors(['section_id', 'subject_id', 'time']);
    }
}
