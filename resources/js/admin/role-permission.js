import axios from "axios";
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('selectAllBtn').addEventListener('click', handleClickSelectAll);
    document.getElementById('saveBtn').addEventListener('click', function(event) {
        console.log(event.target);
        const roleId = parseInt(event.target.getAttribute('data-role-id'));
        handleClickSave(event, roleId);
    });
})

function handleClickSelectAll(event) {
    const table = document.getElementById("permission-table-body")
    const checkboxes = table.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    })
}

function handleClickSave(event, roleId) {    
    const claims = [];
    const table = document.getElementById("permission-table-body")
    const modules = table.querySelectorAll('.module-row');
    modules.forEach(module => {
        const claimName = module.id;
        const checkboxes = module.querySelectorAll('input[type="checkbox"]');
        let totalClaimValue = 0;
        checkboxes.forEach(checkbox => {
            if(checkbox.checked) {
                const claimValue = parseInt(checkbox.value);
                totalClaimValue |= claimValue;
            }
        })
        claims.push({
            claim_name: claimName,
            claim_value: totalClaimValue,
            role_id: roleId
        })
    })
    axios.post(`/admin/roles/${roleId}/permissions`, {
        claims: claims
    })
    .then(response => {
        alert("Cập nhật quyền thành công!");
        window.location.href = `/admin/roles/`;
    })
    .catch(error => {
        console.error('Error updating permissions:', error);
        alert(error.response.data.message);
    });
}