<?php if ($_SESSION['akses'] == 'Warek2' || $_SESSION['akses'] == 'Keuangan') { ?>
    <div class="chat-btn">
        <a href="#">
            <button style="border-radius: 25px; background-color: #fff; width: 40px; height: 40px; cursor: pointer;" onclick="popChat">
                <i class="fa fa-comments" style="color: blue; font-size: 40px;"></i>
            </button>
        </a>
    </div>
    <div>
        <div class="chat-popup" id="Chatbox">
            <form action="/action_page.php" class="form-container">
                <h1>Chat</h1>

                <label for="msg"><b>Message</b></label>
                <textarea placeholder="Type message.." name="msg" required></textarea>

                <button type="submit" class="btn">Send</button>
                <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
            </form>
        </div>
    </div>
<?php } ?>