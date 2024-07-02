<title>Populace Member Profile</title>

<div class="container my-5">
 <div class="row p-4 align-items-center rounded-3 border border-light-subtle shadow-lg">
      <div class="col-lg-7 p-3 p-lg-5">
        <h1 class="display-4 fw-bold lh-1 text-body-emphasis"><?php echo htmlspecialchars($name); ?></h1>
        <p class="lead"><?= isset($branches_name) ? out($branches_name) : 'None' ?></p>
        <ul class="list-group list-group-flush p-2 pb-lg-5">
           <!--  <li class="list-group-item"><strong class="d-inline-block text-primary-emphasis">Preferred Title</strong> <?php echo htmlspecialchars($preferred_title); ?></li> -->
            <li class="list-group-item"><strong class="d-inline-block text-primary-emphasis">Pronouns</strong> <?php echo htmlspecialchars($preferred_pronoun); ?></li>
          <!--   <li class="list-group-item"><strong class="d-inline-block text-primary-emphasis">Pronounciation</strong> <?php echo htmlspecialchars($pronounciation); ?></li> -->
           <!--  <li class="list-group-item"><strong class="d-inline-block text-primary-emphasis">Notes/Alias</strong> <?php echo htmlspecialchars($alias); ?></li> -->
        </ul>
       <!--  <div class="d-flex flex-column flex-md-row gap-3">
            <a href="#" class="btn btn-bd-primary d-flex align-items-center justify-content-center fw-semibold" onclick="ga('Award recommendation');">
              Award recommendation
            </a>
            <a href="#" class="btn btn-outline-secondary" onclick="ga('Submit correction');">
              Submit correction
            </a>
          </div> -->
      </div>
      <div class="col-lg-4 p-3 shadow-lg">
          <img class="img img-fluid" src="populace_members_module/Images/NoImage.jpg" alt="" width="720">
      </div>
    </div>
    <div class="row mt-lg-5">
            <div class="col-md-6">
            <div class="row g-0 border border-light-subtle rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 bg-light position-relative">
                <div class="col p-4 d-flex flex-column position-static">
                <strong class="d-inline-block mb-2 text-primary-emphasis">Blazon</strong>
                <p class="card-text mb-auto"><?php echo htmlspecialchars($blazon); ?></p>
                </div>
                <div class="col-auto d-none p-3 d-lg-block">
                <img class="img img-fluid" src="populace_members_module/Images/NoArms.jpg" alt="" width="150">
                </div>
            </div>
            </div>
            <div class="col-md-6">
            <div class="row g-0 border border-light-subtle rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 bg-light position-relative">
                <div class="col p-4 d-flex flex-column position-static">
                <strong class="d-inline-block mb-2 text-primary-emphasis">Badge(s)</strong>
                <p class="card-text mb-auto"><span class="fst-italic">Not specified</span>
                    </p><p class="card-text mb-auto">Coming Soon</p>
                    <!-- <button type="button" class="btn btn-secondary my-2 px-4" data-bs-toggle="modal" data-bs-target="#badgeModal">See all</button> -->
                </div>
                <div class="col-auto d-none p-3 d-lg-block">
                <img class="img img-fluid" src="populace_members_module/Images/NoArms.jpg" alt="" width="150">
                </div>
            </div>
</div>

  <div class="container">
  <div class="row mt-lg-5"> 
        <div class="">
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">Awards</a>
              </li>
              <li class="nav-item">
                <a class="nav-link disabled" href="#">Offices</a>
              </li>
            </ul>
        </div>
        <div class="p-4 border-bottom">
                    <h2>Awards</h2>
                    <div class="table-responsive small">
                <table class="table table-striped table-sm">
                <thead>
                    <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Rank</th>
                    <th scope="col">Awarded by</th>
                    <th scope="col">Date</th>
                    <th scope="col">Level</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <td><?php ?></td>
                    <td><?php ?></td>
                    <td><?php ?></td>
                    <td><?php ?></td>
                    <td><?php ?></td>
                    </tr>
                </tbody>
                </table>
      </div>
    </div>
    <!--<span class="badge d-flex align-items-center p-1 pe-2 text-warning-emphasis bg-warning-subtle border border-warning-subtle rounded-pill"><img class="rounded-circle me-1" width="24" height="24" src="https://wiki.atenveldt.org/images/4/49/Aten_Beacon_desert.png" alt="Light of Atenveldt"> Service (Level 2)
  </span>-->
        
        </div>
</div>

<div class="modal fade" id="badgeModal" tabindex="-1" aria-labelledby="badgeModalLabel" style="display: none;" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5 text-primary-emphasis" id="badgeModalLabel">Badges</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <ul class="list-group list-group-flush p-2 pb-lg-5">
            <li class="list-group-item"><strong class="d-inline-block text-primary-emphasis">Badge.</strong> <?php echo htmlspecialchars($badge_1); ?></li>
            <li class="list-group-item"><strong class="d-inline-block text-primary-emphasis">Badge.</strong> <?php echo htmlspecialchars($badge_2); ?></li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>