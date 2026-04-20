@extends('layouts.app')

@section('content')


<style>
    body {
            margin: 0;
            min-height: 100vh;
            font-family: Tahoma, Arial, sans-serif;
            background:
                linear-gradient(rgba(15, 23, 42, 0.93), rgba(15, 23, 42, 0.86)),
                url('{{ asset('images/crm.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            color: #0f172a;
        }
.container{
    max-width:1100px;
    margin:80px auto;
    padding:15px;
}

/* HEADER */
.header{
    background:linear-gradient(135deg,#1e293b,#0f172a);
    color:white;
    padding:20px;
    border-radius:16px;
    display:flex;
    gap:20px;
    align-items:center;
}

.avatar{
    width:70px;height:70px;
    border-radius:50%;
    background:#38bdf8;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
    font-weight:bold;
}

/* CARD */
.card{
    background:white;
    margin-top:20px;
    padding:15px;
    border-radius:12px;
}

/* ROW */
.row{
    display:flex;
    justify-content:space-between;
    padding:10px 0;
    border-bottom:1px solid #eee;
}

/* TITLES */
.section-title{
    font-size:18px;
    font-weight:bold;
    border-bottom:2px solid #cfd3d4;
    padding-bottom:6px;
    margin-bottom:12px;
}

/* ICON EDIT */
.icon-btn{
    float:right;
    cursor:pointer;
    color:#38bdf8;
}

/* INPUT */
input{
    width:100%;
    padding:8px;
    margin-top:5px;
}

/* BUTTON */
.btn{
    background:#38bdf8;
    color:white;
    border:none;
    padding:8px 12px;
    border-radius:8px;
    cursor:pointer;
    margin-top:10px;
}

/* ACCORDION */
.accordion{
    cursor:pointer;
    padding:10px;
    background:#e2e8f0;
    border-radius:8px;
    margin-top:10px;
    font-weight:bold;
}

.content{
    display:none;
    padding:10px;
}
</style>

<div class="container">

<!-- HEADER -->
<div class="header">
    <div class="avatar">
        {{ strtoupper(substr(auth()->user()->name,0,1)) }}
    </div>

    <div>
        <h2>{{ auth()->user()->name }}</h2>
        <p>{{ auth()->user()->email }}</p>
    </div>
</div>

<!-- PERSONAL INFO -->
<div class="card">

    <div class="section-title">
        Personal Information
        <span class="icon-btn" onclick="enableEdit()">Edit</span>
    </div>

    <!-- VIEW MODE -->
    <div id="viewMode">

        <div class="row">
            <span>Name</span>
            <strong id="nameView">{{ auth()->user()->name }}</strong>
        </div>

        <div class="row">
            <span>Email</span>
            <strong>{{ auth()->user()->email }}</strong>
        </div>

        <div class="row">
            <span>Phone</span>
            <strong id="phoneView">{{ auth()->user()->phone ?? '-' }}</strong>
        </div>

        <div class="row">
            <span>Position</span>
            <strong id="positionView">{{ auth()->user()->position ?? '-' }}</strong>
        </div>

        <div class="row">
            <span>Role</span>
            <strong>{{ auth()->user()->role ?? 'User' }}</strong>
        </div>

    </div>

    <!-- EDIT MODE -->
    <div id="editMode" style="display:none;">

        <div class="row">
            <span>Name</span>
            <input id="nameInput" value="{{ auth()->user()->name }}">
        </div>

        <div class="row">
            <span>Phone</span>
            <input id="phoneInput" value="{{ auth()->user()->phone }}">
        </div>

        <div class="row">
            <span>Position</span>
            <input id="positionInput" value="{{ auth()->user()->position ?? '' }}">
        </div>


        <button class="btn" onclick="saveProfile()">Save</button>
        <button class="btn" style="background:#64748b;" onclick="cancelEdit()">Cancel</button>

    </div>

</div>

<!-- CHANGE PASSWORD -->
<div class="card">

    <div class="section-title">Change Password</div>

    <div class="accordion" onclick="toggle('passBox')">
        Open
    </div>

    <div class="content" id="passBox">

        <input type="password" id="current_password" placeholder="Current Password">
        <input type="password" id="new_password" placeholder="New Password">
        <input type="password" id="confirm_password" placeholder="Confirm Password">

        <button class="btn" onclick="changePassword()">Update</button>
    </div>

</div>

<!-- SECURITY -->
<div class="card">

    <div class="section-title">Security</div>

    <div class="row">
        <span>Last Login</span>
        <strong>2026-04-20</strong>
    </div>

    <div class="row">
        <span>Account Created</span>
        <strong>{{ auth()->user()->created_at->format('Y-m-d') }}</strong>
    </div>

    <div class="row">
        <span>Permissions</span>
        <strong>Admin Access</strong>
    </div>

    <!-- HISTORY -->
    <div class="accordion" onclick="toggle('historyBox')">
        Login History
    </div>

    <div class="content" id="historyBox">

        @forelse($loginHistories as $log)
            <div class="row">
                <span>{{ $log->login_at }}</span>
                <strong>Login</strong>
            </div>
        @empty
            <p>No history</p>
        @endforelse

    </div>

</div>

</div>

<script>
function toggle(id){
    let el = document.getElementById(id);
    el.style.display = (el.style.display === 'block') ? 'none' : 'block';
}

/* EDIT PROFILE */
function enableEdit(){
    document.getElementById('viewMode').style.display = 'none';
    document.getElementById('editMode').style.display = 'block';
}

function cancelEdit(){
    document.getElementById('viewMode').style.display = 'block';
    document.getElementById('editMode').style.display = 'none';
}

function saveProfile(){
    fetch("{{ route('profile.update') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            _method: "PUT",
            name: document.getElementById('nameInput').value,
            phone: document.getElementById('phoneInput').value,
            position: document.getElementById('positionInput').value
        })
    })
    .then(() => location.reload());
}

/* PASSWORD */
function changePassword(){
    fetch("{{ route('profile.password') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            current_password: document.getElementById('current_password').value,
            password: document.getElementById('new_password').value,
            password_confirmation: document.getElementById('confirm_password').value
        })
    })
    .then(() => location.reload());
}
</script>

@endsection