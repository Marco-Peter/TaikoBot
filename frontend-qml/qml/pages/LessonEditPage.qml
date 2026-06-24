import QtQuick
import QtQuick.Controls.Material
import QtQuick.Layouts
import "../components"

Page {
    id: root
    property int lessonId: 0   // 0 = create new
    property var lesson: ({})
    property int courseId: 0   // required for create

    header: ToolBar {
        TPageHeader {
            anchors.fill: parent
            title: lessonId === 0 ? "New Lesson" : "Edit Lesson"
        }
    }

    ScrollView {
        anchors.fill: parent
        contentWidth: availableWidth

        ColumnLayout {
            width: parent.width
            spacing: 16

            // Title
            TCard {
                Layout.fillWidth: true
                Layout.margins: 12
                height: formCol.implicitHeight + 24

                ColumnLayout {
                    id: formCol
                    anchors { fill: parent; margins: 12 }
                    spacing: 12

                    TextField {
                        id: titleField
                        placeholderText: "Title (optional)"
                        text: lesson.title || ""
                        Layout.fillWidth: true
                    }

                    Label { text: "Start"; font.bold: true }

                    TextField {
                        id: startField
                        placeholderText: "YYYY-MM-DDTHH:MM"
                        text: lesson.start ? new Date(lesson.start).toISOString().slice(0,16) : ""
                        inputMethodHints: Qt.ImhDate | Qt.ImhTime
                        Layout.fillWidth: true
                    }

                    Label { text: "End"; font.bold: true }

                    TextField {
                        id: finishField
                        placeholderText: "YYYY-MM-DDTHH:MM"
                        text: lesson.finish ? new Date(lesson.finish).toISOString().slice(0,16) : ""
                        inputMethodHints: Qt.ImhDate | Qt.ImhTime
                        Layout.fillWidth: true
                    }

                    Label { text: "Notes"; font.bold: true }

                    TextArea {
                        id: notesField
                        placeholderText: "Notes (optional)"
                        text: lesson.notes || ""
                        Layout.fillWidth: true
                        wrapMode: TextEdit.Wrap
                        background: Rectangle {
                            radius: 4
                            border.color: Material.dividerColor
                            color: "transparent"
                        }
                    }

                    // Error banner
                    Label {
                        visible: LessonStore.error.length > 0
                        text: LessonStore.error
                        color: Material.color(Material.Red)
                        wrapMode: Text.Wrap
                        Layout.fillWidth: true
                    }

                    RowLayout {
                        spacing: 8
                        Layout.fillWidth: true

                        TButton {
                            text: lessonId === 0 ? "Create" : "Save"
                            Layout.fillWidth: true
                            enabled: !LessonStore.loading
                            onClicked: save()
                        }

                        TButton {
                            text: "Delete"
                            destructive: true
                            visible: lessonId !== 0
                            enabled: !LessonStore.loading
                            onClicked: deleteDialog.open()
                        }
                    }

                    BusyIndicator {
                        visible: LessonStore.loading
                        Layout.alignment: Qt.AlignHCenter
                    }
                }
            }

            Item { Layout.preferredHeight: 24 }
        }
    }

    Dialog {
        id: deleteDialog
        title: "Delete lesson?"
        modal: true
        anchors.centerIn: Overlay.overlay

        standardButtons: Dialog.Ok | Dialog.Cancel
        onAccepted: {
            LessonStore.deleteLesson(root.lessonId)
            StackView.view.pop()
        }
    }

    Connections {
        target: LessonStore
        function onCurrentLessonChanged() {
            if (!LessonStore.loading && LessonStore.error.length === 0)
                StackView.view.pop()
        }
        function onLessonDeleted() { StackView.view.pop() }
    }

    function save() {
        const data = {
            title:     titleField.text,
            start:     startField.text,
            finish:    finishField.text,
            notes:     notesField.text,
        }
        if (root.courseId > 0) data.course_id = root.courseId

        if (lessonId === 0)
            LessonStore.createLesson(data)
        else
            LessonStore.updateLesson(lessonId, data)
    }
}
