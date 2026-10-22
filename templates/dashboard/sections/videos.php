<!-- Videos Management Section -->
<section id="menu-section-5" class="menu-section <?= ($activeTab === 5) ? 'active' : ''; ?>">
    <div class="currency-container">
        <div style="margin-bottom: 20px;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Management Videos</h2>
        </div>
        <div class="form-section">
            <h3 class="section-title">Upload New Signage Video</h3>
            <form action="<?= $urlFor('videos.upload'); ?>" method="POST" enctype="multipart/form-data" class="currency-form">

                <div class="form-group" style="width: 100%; display: flex; flex-direction: column;">
                    <label for="videoFile" style="font-weight: 500; font-size: 13px; color: #475569; margin-bottom: 6px;">Choose Video File:</label>
                    <input type="file" id="videoFile" name="video_file" accept="video/mp4" required style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; height: 42px; background: #fff;">
                    <small style="color: #64748b; margin-top: 4px;">Supported Format: .mp4 only (Max Size: 50MB)</small>
                </div>

                <button type="submit" name="submit_video" class="btn-submit" style="margin-top: 15px;">Upload Video</button>
            </form>
        </div>

        <hr class="identity-divider" style="margin: 30px 0; border: 0; border-top: 2px dashed #e2e8f0;">

        <div class="table-section">
            <h3 class="section-title">Signage Video Playlists</h3>
            <div class="table-responsive">
                <table class="currency-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Video Preview / File Name</th>
                            <th>Uploaded Date</th>
                            <th style="width: 120px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($videosList)): ?>
                            <?php $no = 1; foreach ($videosList as $row_video): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 15px;">
                                            <video width="120" preload="metadata" class="preview-video-trigger" style="border-radius: 6px; background: #000; box-shadow: 0 1px 3px rgba(0,0,0,0.2); cursor: pointer;" title="Klik untuk putar/jeda dengan suara">
                                                <source src="<?= $basePath; ?>/videos/<?php echo htmlspecialchars((string)$row_video['file_name']); ?>" type="video/mp4">
                                            </video>
                                            <span style="font-family: monospace; font-weight: bold; color: #334155;">
                                                <?php echo htmlspecialchars((string)$row_video['file_name']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo (!empty($row_video['create_at'])) ? date('d M Y', strtotime((string)$row_video['create_at'])) : '-'; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-buttons" style="justify-content: center;">
                                            <a href="<?= $urlFor('videos.delete', ['id' => (string)$row_video['id']]); ?>" class="btn-action-delete"
                                                onclick="return confirm('Are you sure you want to delete the video <?php echo htmlspecialchars((string)$row_video['file_name']); ?>? This action cannot be undone.');">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center; padding: 20px; color: #64748b;">No signage video available. Please upload one.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
