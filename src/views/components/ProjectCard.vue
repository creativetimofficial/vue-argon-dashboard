<template>
  <div class="card">
    <div class="card-header pb-0">
      <div class="row">
        <div class="col-lg-6 col-7">
          <h6>{{ title }}</h6>
          <p class="text-sm mb-0">
            <i class="fa fa-check text-info" aria-hidden="true"></i>
            <span class="font-weight-bold ms-1">{{ descBold }}</span>
            {{ desc }}
          </p>
        </div>
        <div class="col-lg-6 col-5 my-auto text-end">
          <div class="dropdown float-lg-end pe-4">
            <a
              class="cursor-pointer"
              id="dropdownTable"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="fa fa-ellipsis-v text-secondary" aria-hidden="true"></i>
            </a>
            <ul
              class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
              aria-labelledby="dropdownTable"
              style
            >
              <li>
                <a class="dropdown-item border-radius-md" href="javascript:;">{{Action}}</a>
              </li>
              <li>
                <a class="dropdown-item border-radius-md" href="javascript:;">{{AnotherAction}}</a>
              </li>
              <li>
                <a class="dropdown-item border-radius-md" href="javascript:;">{{SomethingElse}}</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body px-0 pb-2">
      <div class="table-responsive">
        <table class="table align-items-center mb-0">
          <thead>
            <tr>
              <th
                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
              >{{ th1 }}</th>
              <th
                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
              >{{ th2 }}</th>
              <th
                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
              >{{ th3 }}</th>
              <th
                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
              >{{ th4 }}</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div class="d-flex py-1">
                  <div>
                    <img
                      src="../../assets/img/small-logos/logo-xd.svg"
                      class="avatar avatar-sm me-3"
                      alt="xd"
                    />
                  </div>
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm me-3">Material XD {{data1}}</h6>
                  </div>
                </div>
              </td>
              <td>
                <div class="avatar-group mt-2">
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Ryan Tompson"
                  >
                    <img src="../../assets/img/team-1.jpg" alt="team1" />
                  </a>
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Romina Hadid"
                  >
                    <img src="../../assets/img/team-2.jpg" alt="team2" />
                  </a>
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Alexander Smith"
                  >
                    <img src="../../assets/img/team-3.jpg" alt="team3" />
                  </a>
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Jessica Doe"
                  >
                    <img src="../../assets/img/team-4.jpg" alt="team4" />
                  </a>
                </div>
              </td>
              <td class="align-middle text-center text-sm">
                <span class="text-xs font-weight-bold">$14,000</span>
              </td>
              <td class="align-middle">
                <div class="progress-wrapper w-75 mx-auto">
                  <div class="progress-info">
                    <div class="progress-percentage">
                      <span class="text-xs font-weight-bold">60%</span>
                    </div>
                  </div>
                  <div class="progress">
                    <div
                      class="progress-bar bg-gradient-info w-60"
                      role="progressbar"
                      aria-valuenow="60"
                      aria-valuemin="0"
                      aria-valuemax="100"
                    ></div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="d-flex py-1">
                  <div>
                    <img
                      src="../../assets/img/small-logos/logo-atlassian.svg"
                      class="avatar avatar-sm me-3"
                      alt="atlassian"
                    />
                  </div>
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm me-3">{{ data2 }}</h6>
                  </div>
                </div>
              </td>
              <td>
                <div class="avatar-group mt-2">
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Romina Hadid"
                  >
                    <img src="../../assets/img/team-2.jpg" alt="team5" />
                  </a>
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Jessica Doe"
                  >
                    <img src="../../assets/img/team-4.jpg" alt="team6" />
                  </a>
                </div>
              </td>
              <td class="align-middle text-center text-sm">
                <span class="text-xs font-weight-bold">$3,000</span>
              </td>
              <td class="align-middle">
                <div class="progress-wrapper w-75 mx-auto">
                  <div class="progress-info">
                    <div class="progress-percentage">
                      <span class="text-xs font-weight-bold">10%</span>
                    </div>
                  </div>
                  <div class="progress">
                    <div
                      class="progress-bar bg-gradient-info w-10"
                      role="progressbar"
                      aria-valuenow="10"
                      aria-valuemin="0"
                      aria-valuemax="100"
                    ></div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="d-flex py-1">
                  <div>
                    <img
                      src="../../assets/img/small-logos/logo-slack.svg"
                      class="avatar avatar-sm me-3"
                      alt="team7"
                    />
                  </div>
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm me-3">{{ data3 }}</h6>
                  </div>
                </div>
              </td>
              <td>
                <div class="avatar-group mt-2">
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Romina Hadid"
                  >
                    <img src="../../assets/img/team-3.jpg" alt="team8" />
                  </a>
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Jessica Doe"
                  >
                    <img src="../../assets/img/team-1.jpg" alt="team9" />
                  </a>
                </div>
              </td>
              <td class="align-middle text-center text-sm">
                <span class="text-xs font-weight-bold">{{ empty }}</span>
              </td>
              <td class="align-middle">
                <div class="progress-wrapper w-75 mx-auto">
                  <div class="progress-info">
                    <div class="progress-percentage">
                      <span class="text-xs font-weight-bold">100%</span>
                    </div>
                  </div>
                  <div class="progress">
                    <div
                      class="progress-bar bg-gradient-success w-100"
                      role="progressbar"
                      aria-valuenow="100"
                      aria-valuemin="0"
                      aria-valuemax="100"
                    ></div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="d-flex py-1">
                  <div>
                    <img
                      src="../../assets/img/small-logos/logo-spotify.svg"
                      class="avatar avatar-sm me-3"
                      alt="spotify"
                    />
                  </div>
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm me-3">{{ data4 }}</h6>
                  </div>
                </div>
              </td>
              <td>
                <div class="avatar-group mt-2">
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Ryan Tompson"
                  >
                    <img src="../../assets/img/team-4.jpg" alt="user1" />
                  </a>
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Romina Hadid"
                  >
                    <img src="../../assets/img/team-3.jpg" alt="user2" />
                  </a>
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Alexander Smith"
                  >
                    <img src="../../assets/img/team-4.jpg" alt="user3" />
                  </a>
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Jessica Doe"
                  >
                    <img src="../../assets/img/team-1.jpg" alt="user4" />
                  </a>
                </div>
              </td>
              <td class="align-middle text-center text-sm">
                <span class="text-xs font-weight-bold">$20,500</span>
              </td>
              <td class="align-middle">
                <div class="progress-wrapper w-75 mx-auto">
                  <div class="progress-info">
                    <div class="progress-percentage">
                      <span class="text-xs font-weight-bold">100%</span>
                    </div>
                  </div>
                  <div class="progress">
                    <div
                      class="progress-bar bg-gradient-success w-100"
                      role="progressbar"
                      aria-valuenow="100"
                      aria-valuemin="0"
                      aria-valuemax="100"
                    ></div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="d-flex py-1">
                  <div>
                    <img
                      src="../../assets/img/small-logos/logo-jira.svg"
                      class="avatar avatar-sm me-3"
                      alt="jira"
                    />
                  </div>
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm me-3">{{ data5 }}</h6>
                  </div>
                </div>
              </td>
              <td>
                <div class="avatar-group mt-2">
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Ryan Tompson"
                  >
                    <img src="../../assets/img/team-4.jpg" alt="user5" />
                  </a>
                </div>
              </td>
              <td class="align-middle text-center text-sm">
                <span class="text-xs font-weight-bold">$500</span>
              </td>
              <td class="align-middle">
                <div class="progress-wrapper w-75 mx-auto">
                  <div class="progress-info">
                    <div class="progress-percentage">
                      <span class="text-xs font-weight-bold">25%</span>
                    </div>
                  </div>
                  <div class="progress">
                    <div
                      class="progress-bar bg-gradient-info w-25"
                      role="progressbar"
                      aria-valuenow="25"
                      aria-valuemin="0"
                      aria-valuemax="25"
                    ></div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="d-flex py-1">
                  <div>
                    <img
                      src="../../assets/img/small-logos/logo-invision.svg"
                      class="avatar avatar-sm me-3"
                      alt="invision"
                    />
                  </div>
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm me-3">{{ data6 }}</h6>
                  </div>
                </div>
              </td>
              <td>
                <div class="avatar-group mt-2">
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Ryan Tompson"
                  >
                    <img src="../../assets/img/team-1.jpg" alt="user6" />
                  </a>
                  <a
                    href="javascript:;"
                    class="avatar avatar-xs rounded-circle"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title
                    data-bs-original-title="Jessica Doe"
                  >
                    <img src="../../assets/img/team-4.jpg" alt="user7" />
                  </a>
                </div>
              </td>
              <td class="align-middle text-center text-sm">
                <span class="text-xs font-weight-bold">$2,000</span>
              </td>
              <td class="align-middle">
                <div class="progress-wrapper w-75 mx-auto">
                  <div class="progress-info">
                    <div class="progress-percentage">
                      <span class="text-xs font-weight-bold">40%</span>
                    </div>
                  </div>
                  <div class="progress">
                    <div
                      class="progress-bar bg-gradient-info w-40"
                      role="progressbar"
                      aria-valuenow="40"
                      aria-valuemin="0"
                      aria-valuemax="40"
                    ></div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "project-card",
  props: {
    Action: {
      type: String,
      default: "Action",
    },
    AnotherAction: {
      type: String,
      default: "Another Action",
    },
    SomethingElse: {
      type: String,
      default: "Something else here",
    },
    th1: {
      type: String,
      default: "COMPANIES",
    },
    th2: {
      type: String,
      default: "MEMBERS",
    },
    th3: {
      type: String,
      default: "BUDGET",
    },
    th4: {
      type: String,
      default: "COMPLETION",
    },
    title: {
      type: String,
      default: "Projects table",
    },
    desc: {
      type: String,
      default: "this month",
    },
    descBold: {
      type: String,
      default: "30 done",
    },
    data1: {
      type: String,
      default: "Material XD Version",
    },
    data2: {
      type: String,
      default: "Add Progress Track",
    },
    data3: {
      type: String,
      default: "Fix Platform Errors",
    },
    data4: {
      type: String,
      default: "Launch our Mobile App",
    },
    data5: {
      type: String,
      default: "Add the New Pricing Page",
    },
    data6: {
      type: String,
      default: "Redesign New Online Shop",
    },
    empty: {
      type: String,
      default: "Not set",
    },
  },
};
</script>