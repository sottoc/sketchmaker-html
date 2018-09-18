<script type="text/template" data-template="addUsersForm">
  <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
       aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">${title}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="accountData">
            <input type="hidden" name="id" value="${id}">
            <div class="form-group">
              <label for="inputEmail">Email</label>
              <input type="text" class="form-control" id="inputEmail" name="email" placeholder="Email" value="${email}">
            </div>
            <div class="form-group">
              <label for="inputPassword">Password</label>
              <input type="text" class="form-control" id="inputPassword" name="password" value="${password}">
            </div>
            <div class="form-group">
              <label for="inputLimit">Limit</label>
              <select id="inputLimit" class="form-control" name="limit">
                <option selected value="10">10 per day</option>
                <option value="0">Unlimited</option>
              </select>
            </div>
            <div class="form-group">
              <label for="inputLicense">License</label>
              <input type="text" class="form-control" id="inputLicense" name="license" placeholder="License" value="${license}">
            </div>            
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="${action}">${title}</button>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" data-template="usersTableRow">
  <tr>
    <td>${id}</td>
    <td>${isAdmin}</td>
    <td>${email}</td>
    <td>${limit}</td>
    <td>${license}</td>
    <td>${addedOn}</td>
    <td>
      <button type="button" class="btn btn-sm btn-info emailLicenseButton" data-toggle="modal" data-user="${id}" >
        Email License
      </button>
      <button type="button" class="btn btn-sm btn-primary editUserButton" data-toggle="modal" data-user="${id}" data-email="${email}" data-license="${license}">
        Edit user
      </button>
      <button type="button" class="btn btn-sm btn-danger deleteUserButton" data-toggle="modal" data-user="${id}">
        Delete user
      </button>
    </td>
  </tr>
</script>
