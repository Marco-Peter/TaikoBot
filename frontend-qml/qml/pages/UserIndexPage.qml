import QtQuick
import QtQuick.Controls.Material
import QtQuick.Layouts
import "../components"

Page {
    id: root

    header: ToolBar {
        RowLayout {
            anchors.fill: parent
            anchors.leftMargin: 12
            anchors.rightMargin: 8

            Label {
                text: "Members"
                font.pixelSize: 18
                font.bold: true
                Layout.fillWidth: true
            }
        }
    }

    ColumnLayout {
        anchors.fill: parent
        spacing: 0

        // Search bar
        TextField {
            id: searchField
            placeholderText: "Search by name…"
            Layout.fillWidth: true
            Layout.leftMargin: 12
            Layout.rightMargin: 12
            Layout.topMargin: 8
            leftPadding: 8
            onTextChanged: filterUsers()
        }

        ListView {
            id: listView
            Layout.fillWidth: true
            Layout.fillHeight: true
            clip: true
            model: filteredUsers
            spacing: 4

            ScrollBar.vertical: ScrollBar {}

            delegate: ItemDelegate {
                width: listView.width
                height: 56

                RowLayout {
                    anchors { fill: parent; leftMargin: 16; rightMargin: 16 }
                    spacing: 12

                    // Role indicator
                    Rectangle {
                        width: 8; height: 8; radius: 4
                        color: {
                            switch (modelData.role) {
                            case "admin":   return Material.color(Material.Red)
                            case "teacher": return Material.color(Material.Blue)
                            default:        return Material.color(Material.Green)
                            }
                        }
                    }

                    ColumnLayout {
                        spacing: 0
                        Layout.fillWidth: true

                        Label {
                            text: modelData.first_name + " " + modelData.last_name +
                                  (modelData.nickname ? " "" + modelData.nickname + """ : "")
                            font.bold: true
                            elide: Text.ElideRight
                            Layout.fillWidth: true
                        }
                        Label {
                            text: modelData.role
                            font.pixelSize: 11
                            opacity: 0.6
                        }
                    }

                    TKarmaDisplay { karma: modelData.karma }
                }

                onClicked: StackView.view.push("UserEditPage.qml",
                               { userId: modelData.id })
            }

            BusyIndicator {
                visible: UserStore.loading
                anchors.centerIn: parent
            }
        }
    }

    property var filteredUsers: UserStore.users

    function filterUsers() {
        const q = searchField.text.toLowerCase()
        if (q.length === 0) {
            filteredUsers = UserStore.users
            return
        }
        filteredUsers = UserStore.users.filter(function(u) {
            return (u.first_name + " " + u.last_name).toLowerCase().includes(q)
        })
    }

    Component.onCompleted: UserStore.loadUsers()

    Connections {
        target: UserStore
        function onUsersChanged() { filterUsers() }
    }
}
