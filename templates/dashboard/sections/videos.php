<!-- Videos Management Section -->
<section id="menu-section-5" class="menu-section <?= ($activeTab === 5) ? 'active' : ''; ?>">
    <div class="currency-container">
        <div class="section-page-header">
            <div>
                <h2>Management Videos</h2>
                <small>Upload and organize MP4 video playlists for the signage display</small>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--indigo)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                Upload New Signage Video
            </h3>
            <form action="<?= $urlFor('videos.upload'); ?>" method="POST" enctype="multipart/form-data" class="currency-form">
                <div class="form-group video-upload-box" style="width: 100%;">
                    <label for="videoFile">Choose MP4 Video File</label>
                    <input type="file" id="videoFile" name="video_file" accept="video/mp4" required style="width: 100%; height: auto; padding: 9px 12px;">
                    <small style="color: var(--text-muted); margin-top: 6px; font-size: 12px;">Supported Format: .mp4 only (Max Size: 50MB)</small>
                </div>

                <button type="submit" name="submit_video" class="btn-submit" style="margin-top: 6px;">Upload Video</button>
            </form>
        </div>

        <div class="table-section">
            <h3 class="section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--sky)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="23 7 16 12 23 17 23 7"></polygon>
                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                </svg>
                Signage Video Playlists
            </h3>
            <div class="table-responsive">
                <table class="currency-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">No</th>
                            <th>Video Preview &amp; File Name</th>
                            <th>Uploaded Date</th>
                            <th style="width: 130px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($videosList)): ?>
                            <?php $no = 1; foreach ($videosList as $row_video): ?>
                                <tr>
                                    <td><strong><?php echo $no++; ?></strong></td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
                                            <video width="130" preload="metadata" class="preview-video-trigger" style="border-radius: 10px; background: #000; box-shadow: 0 4px 10px rgba(0,0,0,0.16); cursor: pointer;" title="Click to play/pause with sound">
                                                <source src="<?= $basePath; ?>/videos/<?php echo htmlspecialchars((string)$row_video['file_name']); ?>" type="video/mp4">
                                            </video>
                                            <span class="video-file-badge">
                                                <?php echo htmlspecialchars((string)$row_video['file_name']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="video-date-pill">
                                            <?php echo (!empty($row_video['create_at'])) ? date('d M Y', strtotime((string)$row_video['create_at'])) : '-'; ?>
                                        </span>
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
                            <tr><td colspan="4" style="text-align:center; padding: 22px; color: var(--text-muted);">No signage video available. Please upload one.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
