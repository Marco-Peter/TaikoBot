import QtQuick
import QtQuick.Controls.Material
import QtQuick.Layouts
import "../components"

Page {
    id: root
    property int userId: 0

    header: ToolBar {
        TPageHeader {
            anchors.fill: parent
            title: {
                const u = UserStore.currentUser
                return u.first_name ? (u.first_name + " " + u.last_name) : "Member"
            }
        }
    }

    ScrollView {
        anchors.fill: parent
        contentWidth: availableWidth

        ColumnLayout {
            width: parent.width
            spacing: 16

            // Profile info card
            TCard {
                Layout.fillWidth: true
                Layout.margins: 12
                height: profileCol.implicitHeight + 24

                ColumnLayout {
                    id: profileCol
                    anchors { fill: parent; margins: 12 }
                    spacing: 8

                    RowLayout {
                        Label { text: "Karma:"; opacity: 0.7 }
                        TKarmaDisplay { karma: UserStore.currentUser.karma }
                    }

                    RowLayout {
                        Label { text: "Email:"; opacity: 0.7 }
                        Label {
                            text: UserStore.currentUser.email || ""
                            Layout.fillWidth: true
                        }
                    }

                    RowLayout {
                        visible: (UserStore.currentUser.open_payment || 0) !== 0
                        Label { text: "Open payment:"; opacity: 0.7 }
                        Label {
                            text: "CHF " + (UserStore.currentUser.open_payment || 0)
                            font.bold: true
                            color: Material.color(Material.Orange, Material.Shade700)
                        }
                    }
                }
            }

            // Role edit (admin only)
            TCard {
                visible: AuthStore.canEditUsers
                Layout.fillWidth: true
                Layout.leftMargin: 12
                Layout.rightMargin: 12
                height: roleCol.implicitHeight + 24

                ColumnLayout {
                    id: roleCol
                    anchors { fill: parent; margins: 12 }
                    spacing: 8

                    Label { text: "Role"; font.bold: true }

                    ComboBox {
                        id: roleCombo
                        model: ["student", "teacher", "admin"]
                        currentIndex: model.indexOf(UserStore.currentUser.role || "student")
                        Layout.fillWidth: true
                    }

                    TButton {
                        text: "Update role"
                        enabled: !UserStore.loading
                        onClicked: UserStore.updateUser(root.userId, { role: roleCombo.currentText })
                    }
                }
            }

            // Add payment
            TCard {
                visible: AuthStore.canEditUsers
                Layout.fillWidth: true
                Layout.leftMargin: 12
                Layout.rightMargin: 12
                height: paymentCol.implicitHeight + 24

                ColumnLayout {
                    id: paymentCol
                    anchors { fill: parent; margins: 12 }
                    spacing: 8

                    Label { text: "Add payment"; font.bold: true }

                    RowLayout {
                        Label { text: "CHF" }
                        SpinBox {
                            id: paymentAmount
                            from: -9999; to: 9999
                            value: 0
                            Layout.fillWidth: true
                        }
                    }

                    TextField {
                        id: paymentNotes
                        placeholderText: "Notes (optional)"
                        Layout.fillWidth: true
                    }

                    TButton {
                        text: "Record payment"
                        enabled: !UserStore.loading && paymentAmount.value !== 0
                        onClicked: {
                            UserStore.addPayment(root.userId, paymentAmount.value, paymentNotes.text)
                            paymentAmount.value = 0
                            paymentNotes.text = ""
                        }
                    }
                }
            }

            // Error
            Label {
                visible: UserStore.error.length > 0
                text: UserStore.error
                color: Material.color(Material.Red)
                wrapMode: Text.Wrap
                Layout.leftMargin: 12
                Layout.rightMargin: 12
            }

            BusyIndicator {
                visible: UserStore.loading
                Layout.alignment: Qt.AlignHCenter
            }

            Item { Layout.preferredHeight: 24 }
        }
    }

    Component.onCompleted: UserStore.loadUser(userId)
}
