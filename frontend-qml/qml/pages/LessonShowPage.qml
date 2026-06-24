import QtQuick
import QtQuick.Controls.Material
import QtQuick.Layouts
import "../components"

Page {
    id: root
    property int lessonId: 0

    header: ToolBar {
        TPageHeader {
            anchors.fill: parent
            title: LessonStore.currentLesson.title || LessonStore.currentLesson.course_name || "Lesson"
        }
    }

    TOfflineBanner {
        id: offlineBanner
        anchors { top: parent.top; left: parent.left; right: parent.right }
        pendingCount: SyncEngine.pendingCount
        visible: !SyncEngine.online
    }

    ScrollView {
        anchors {
            top: offlineBanner.visible ? offlineBanner.bottom : parent.top
            left: parent.left
            right: parent.right
            bottom: actionBar.top
        }
        contentWidth: availableWidth

        ColumnLayout {
            width: parent.width
            spacing: 8

            // Date / time
            TCard {
                Layout.fillWidth: true
                Layout.margins: 12
                height: dateCol.implicitHeight + 24

                ColumnLayout {
                    id: dateCol
                    anchors { fill: parent; margins: 12 }
                    spacing: 4

                    Label {
                        text: Qt.formatDateTime(new Date(lesson.start), "dddd, dd MMMM yyyy")
                        font.bold: true
                        font.pixelSize: 15
                    }
                    Label {
                        text: Qt.formatDateTime(new Date(lesson.start), "HH:mm") + " – " +
                              Qt.formatDateTime(new Date(lesson.finish), "HH:mm")
                        opacity: 0.7
                    }
                    Label {
                        visible: lesson.notes && lesson.notes.length > 0
                        text: lesson.notes || ""
                        wrapMode: Text.Wrap
                        Layout.fillWidth: true
                        topPadding: 4
                    }
                }
            }

            // Participants (teachers/admin see everyone; students see count + own status)
            Label {
                text: "Participants (" + (lesson.participants_count || 0) + ")"
                font.bold: true
                font.pixelSize: 14
                color: Material.accent
                Layout.leftMargin: 12
            }

            Repeater {
                model: AuthStore.canAssistLessons ? LessonStore.participants : []

                TCard {
                    required property var modelData
                    required property int index
                    Layout.fillWidth: true
                    Layout.leftMargin: 12
                    Layout.rightMargin: 12
                    height: participantRow.implicitHeight + 16

                    RowLayout {
                        id: participantRow
                        anchors { fill: parent; margins: 8 }

                        Label {
                            text: modelData.first_name + " " + modelData.last_name
                            Layout.fillWidth: true
                        }

                        // Attendance picker for teachers
                        ComboBox {
                            visible: AuthStore.canAssistLessons &&
                                     (modelData.participation === "signed_in" ||
                                      modelData.participation === "late" ||
                                      modelData.participation === "no_show")
                            model: ["signed_in", "late", "no_show"]
                            currentIndex: model.indexOf(modelData.participation)
                            onActivated: {
                                LessonStore.setAttendance(
                                    root.lessonId,
                                    modelData.user_id,
                                    model[index]
                                )
                            }
                            implicitWidth: 120
                        }

                        TParticipationBadge {
                            visible: !AuthStore.canAssistLessons ||
                                     !(modelData.participation === "signed_in" ||
                                       modelData.participation === "late" ||
                                       modelData.participation === "no_show")
                            participation: modelData.participation || ""
                        }
                    }
                }
            }

            // Student: own status card
            TCard {
                visible: !AuthStore.canAssistLessons && myParticipation !== ""
                Layout.fillWidth: true
                Layout.leftMargin: 12
                Layout.rightMargin: 12
                height: myRow.implicitHeight + 20

                RowLayout {
                    id: myRow
                    anchors { fill: parent; margins: 12 }
                    Label { text: "My status:"; Layout.fillWidth: true }
                    TParticipationBadge { participation: myParticipation }
                }
            }

            // Loading
            BusyIndicator {
                visible: LessonStore.loading
                Layout.alignment: Qt.AlignHCenter
            }

            // Edit button (teachers/admin)
            TButton {
                visible: AuthStore.canEditCourses
                text: "Edit lesson"
                Layout.alignment: Qt.AlignHCenter
                Layout.bottomMargin: 8
                onClicked: StackView.view.push("LessonEditPage.qml",
                               { lessonId: root.lessonId, lesson: LessonStore.currentLesson })
            }

            Item { Layout.preferredHeight: 8 }
        }
    }

    // Student sign-in / sign-out bar
    ToolBar {
        id: actionBar
        visible: !AuthStore.canAssistLessons
        anchors { left: parent.left; right: parent.right; bottom: parent.bottom }

        RowLayout {
            anchors { fill: parent; margins: 8 }
            spacing: 8

            TButton {
                text: "Sign in"
                visible: canSignIn
                enabled: SyncEngine.online
                Layout.fillWidth: true
                onClicked: LessonStore.signIn(root.lessonId)
            }

            TButton {
                text: "Compensate"
                visible: canCompensate
                enabled: SyncEngine.online
                Layout.fillWidth: true
                onClicked: LessonStore.compensate(root.lessonId)
            }

            TButton {
                text: "Sign out"
                destructive: true
                visible: canSignOut
                enabled: SyncEngine.online
                Layout.fillWidth: true
                onClicked: LessonStore.signOut(root.lessonId)
            }
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    property var lesson: LessonStore.currentLesson
    property string myParticipation: {
        for (var i = 0; i < LessonStore.participants.length; i++) {
            if (LessonStore.participants[i].user_id === AuthStore.userId)
                return LessonStore.participants[i].participation
        }
        return ""
    }
    property bool canSignIn:    myParticipation === "" || myParticipation === "signed_out"
    property bool canSignOut:   myParticipation === "signed_in" || myParticipation === "waitlist"
    property bool canCompensate: false  // set by compensation_available flag from API

    Component.onCompleted: LessonStore.loadLesson(lessonId)

    Connections {
        target: LessonStore
        function onParticipationChanged(id) { if (id === root.lessonId) LessonStore.loadLesson(id) }
    }
}
