<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="admin-page">
    <div class="admin-card">
        <h2>Edit About Page</h2>
        <?php if ($this->session->flashdata('admin_msg')): ?>
            <div class="admin-alert success"><?= htmlspecialchars($this->session->flashdata('admin_msg')); ?></div>
        <?php endif; ?>

        <?php $a = isset($about) && is_array($about) ? $about : []; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-12">
                    <h3>Hero</h3>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Hero Title</label>
                        <input type="text" name="hero_title" value="<?= htmlspecialchars($a['hero_title'] ?? 'About Us'); ?>" class="form-control" />
                    </div>
                </div>
            </div>

            <h3>Journey</h3>
            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Journey Title</label>
                        <input type="text" name="journey_title" value="<?= htmlspecialchars($a['journey_title'] ?? 'Our Journey Began With a Simple Dream'); ?>" class="form-control" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lead paragraph 1</label>
                        <textarea name="journey_lead1" class="form-control" rows="3"><?= htmlspecialchars($a['journey_lead1'] ?? 'Our goal is to make the best ice cream using only the finest, natural ingredients.'); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lead paragraph 2</label>
                        <textarea name="journey_lead2" class="form-control" rows="3"><?= htmlspecialchars($a['journey_lead2'] ?? 'We take pride in offering a diverse range of options.'); ?></textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Journey Image (optional)</label>
                        <?php if (!empty($a['journey_image'])): ?><div class="mb-2"><img id="preview-journey" src="<?= base_url($a['journey_image']); ?>" class="img-fluid rounded" style="max-width:220px"></div><?php else: ?><div class="mb-2"><img id="preview-journey" src="<?= base_url('assets/images/placeholder.svg'); ?>" class="img-fluid rounded" style="max-width:220px"></div><?php endif; ?>
                        <div class="input-group">
                            <input type="file" id="journey_image" name="journey_image" accept="image/*" class="form-control" />
                            <button type="button" class="btn btn-outline-secondary" id="btn-replace-journey">Pilih & Ganti</button>
                        </div>
                    </div>
                </div>
            </div>

            <h3>Mission</h3>
            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Mission Title</label>
                        <input type="text" name="mission_title" value="<?= htmlspecialchars($a['mission_title'] ?? 'Our Mission is to Create Moments'); ?>" class="form-control" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mission Lead</label>
                        <textarea name="mission_lead" class="form-control" rows="3"><?= htmlspecialchars($a['mission_lead'] ?? 'We strive to foster a welcoming and joyful environment.'); ?></textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Mission Image (optional)</label>
                        <?php if (!empty($a['mission_image'])): ?><div class="mb-2"><img src="<?= base_url($a['mission_image']); ?>" class="img-fluid rounded" style="max-width:220px"></div><?php endif; ?>
                        <div class="input-group">
                            <input type="file" id="mission_image" name="mission_image" accept="image/*" class="form-control" />
                            <button type="button" class="btn btn-outline-secondary" id="btn-replace-mission">Pilih & Ganti</button>
                        </div>
                    </div>
                </div>
            </div>

            <h3>Team Members</h3>
            <p class="muted">Edit up to 6 team members (name, role, optional image).</p>
            <?php $team = isset($a['team']) && is_array($a['team']) ? $a['team'] : []; ?>
            <?php for ($i=0;$i<6;$i++):
                $member = $team[$i] ?? ['name'=>'','role'=>'','image'=>''];
            ?>
                <div class="row align-items-center mb-3">
                    <div class="col-md-3 text-center">
                        <?php if (!empty($member['image'])): ?><img id="preview-team-<?= $i; ?>" src="<?= base_url($member['image']); ?>" class="img-fluid rounded mb-2" style="max-width:140px"><?php else: ?><img id="preview-team-<?= $i; ?>" src="<?= base_url('assets/images/placeholder.svg'); ?>" class="img-fluid rounded mb-2" style="max-width:140px"><?php endif; ?>
                        <div class="mb-2 input-group">
                            <input type="file" id="team_image_<?= $i; ?>" name="team_image_<?= $i; ?>" accept="image/*" class="form-control form-control-sm" />
                            <button type="button" class="btn btn-outline-secondary btn-replace-team" data-target="team_image_<?= $i; ?>">Pilih & Ganti</button>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Member <?= $i+1; ?> Name</label>
                                <input type="text" name="team_name[<?= $i; ?>]" value="<?= htmlspecialchars($member['name']); ?>" class="form-control" />
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Member <?= $i+1; ?> Role</label>
                                <input type="text" name="team_role[<?= $i; ?>]" value="<?= htmlspecialchars($member['role']); ?>" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>

            <div class="form-actions mt-4">
                <button class="btn btn-primary" type="submit">Save About</button>
                <a class="btn btn-secondary" href="<?= base_url('index.php/admin'); ?>">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
// wire replace buttons to trigger file inputs and show a client-side preview
document.addEventListener('DOMContentLoaded', function(){
    var btnJourney = document.getElementById('btn-replace-journey');
    var inpJourney = document.getElementById('journey_image');
    if (btnJourney && inpJourney) {
        btnJourney.addEventListener('click', function(){ inpJourney.click(); });
        inpJourney.addEventListener('change', function(e){
            var f = e.target.files && e.target.files[0];
            if (!f) return;
            var url = URL.createObjectURL(f);
            var img = document.getElementById('preview-journey');
            if (img) img.src = url;
        });
    }

    var btnMission = document.getElementById('btn-replace-mission');
    var inpMission = document.getElementById('mission_image');
    if (btnMission && inpMission) {
        btnMission.addEventListener('click', function(){ inpMission.click(); });
        inpMission.addEventListener('change', function(e){
            var f = e.target.files && e.target.files[0];
            if (!f) return;
            var url = URL.createObjectURL(f);
            var img = document.getElementById('preview-mission');
            if (img) img.src = url;
        });
    }

    var teamBtns = document.querySelectorAll('.btn-replace-team');
    teamBtns.forEach(function(b){
        var target = b.getAttribute('data-target');
        var inp = document.getElementById(target);
        if (!inp) return;
        b.addEventListener('click', function(){ inp.click(); });
        inp.addEventListener('change', function(e){
            var f = e.target.files && e.target.files[0];
            if (!f) return;
            var url = URL.createObjectURL(f);
            var preview = document.getElementById('preview-' + target.replace('_','-')) || document.getElementById('preview-' + target);
            // preview id is preview-team-<i>
            if (!preview) {
                // fallback for known pattern
                var p = target.replace('team_image_','');
                preview = document.getElementById('preview-team-' + p);
            }
            if (preview) preview.src = url;
        });
    });
});
</script>
