<?php
    session_start();
    include_once "./includes/connect_db.php";

    if (isset($_POST['input'])) {
        $input = '%' . $_POST['input'] . '%';
        $stmt = $pdo->prepare("
            SELECT 
                user.iduser as 'iduser',
                user.student_no as 'student_no',
                user.f_name as 'f_name',
                user.l_name as 'l_name',
                organization.abbreviation as 'program',
                user.organization as 'idprogram_user',
                user.year as 'year',
                user.block as 'block',
                user.email as 'email',
                user.is_officer as 'is_officer',
                user.is_superuser as 'is_superuser',
                user.is_admin as 'is_admin',
                user.total_points as 'total_points',
                user.profile_pic as 'profile_pic'
            FROM user
            INNER JOIN organization
            ON user.organization = organization.idorganization
            WHERE (user.is_officer <> 1) AND (user.is_superuser <> 1) and (user.is_admin <> 1) AND
            (f_name LIKE :input1 OR l_name LIKE :input2)
            ORDER BY user.year, user.block, user.f_name
            "
        );
        
        $stmt->bindParam(':input1', $input, PDO::PARAM_STR);
        $stmt->bindParam(':input2', $input, PDO::PARAM_STR);

        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($students) > 0) {
            foreach ($students as $student): ?>
                <div id="student-<?php echo $student['iduser'] ?>"
                    data-student_no="<?php echo $student['student_no'] ?>" data-f_name="<?php echo $student['f_name'] ?>"
                    data-l_name="<?php echo $student['l_name'] ?>" data-idprogram="<?php echo $student['idprogram_user'] ?>"
                    data-year="<?php echo $student['year'] ?>" data-block="<?php echo $student['block'] ?>"
                    data-email="<?php echo $student['email'] ?>"
                    data-profile_pic="<?= base64_encode($student['profile_pic']) ?>" data-user_type="<?php if ($student['is_officer'] == 1) {
                          echo "1";
                      } else if ($student['is_superuser'] == 1) {
                          echo "2";
                      } else if ($student['is_admin'] == 1) {
                          echo "3";
                      } else {
                          echo "0";
                      } ?>" data-total_points="<?php echo $student['total_points'] ?>"
                    class="flex flex-col w-full gap-2 mt-2 bg-white md:mt-0 h-fit md:justify-center md:items-center">
                    <div id=""
                        onclick="showEditStudentModal(<?php echo $student['iduser'] ?>)"
                        class="relative flex w-full md:w-3/4 md:h-auto md:items-stretch p-1 md:p-0 border border-[#b7b9b9] bg-[#EDF4F2] hover:bg-[#dde4e2e0] h-fit cursor-pointer items-center">
                        <!-- Image -->
                        <div class="flex h-full min-w-16 w-16 mr-2 justify-center items-center px-1 md:absolute md:-left-[2.5rem] md:min-w-0 md:w-fit md:p-1 md:bg-emerald-700/20 md:rounded-l-lg cursor-default">
                            <img class="w-full border border-gray-300 rounded-full md:w-8" src="data:image/jpeg;base64, <?= base64_encode($student['profile_pic']) ?>">
                        </div>
                        
                        <!-- Information -->
                        <div class="flex flex-col w-auto h-full md:w-full md:h-auto md:flex-row">
                            <div
                                class="flex items-center w-[15rem] text-wrap h-fit font-bold md:py-1 font-['mulish'] text-[1.5rem] md:text-[1.3rem] md:w-1/4 md:h-auto md:px-1 md:border-r-2 md:border-[#b7b9b9] md:font-medium">
                                <p class="md:w-3/4"><?= $student['f_name'] ?> <?= $student['l_name'] ?></p>
                            </div>
                            <div
                                class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:py-1 text-zinc-600 md:w-1/4 md:text-[1.3rem] md:px-1 md:h-full md:text-black md:border-r-2 md:border-[#b7b9b9] md:font-medium">
                                <?= $student['student_no'] ?>
                            </div>
                            <div
                                class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/4 md:py-1 md:px-1 md:h-full md:text-[1.3rem] md:font-medium">
                                <?= $student['program'] ?>         <?= $student['year'] ?> Block <?= $student['block'] ?>
                            </div>
                            <div
                                class="absolute top-0 flex flex-col justify-center items-center h-full p-1 text-white bg-zinc-600 font-['mulish'] align-center right-0 min-w-16 md:right-0 md:text-[1.3rem] md:w-1/4 md:h-full md:px-1">
                                <p class="text-lg"><?= $student['total_points'] ?></p>
                                <p class="text-xs md:hidden">Points</p>
                            </div>
                        </div>
                    </div>

                </div>

            <?php endforeach; 
        }
    } ?>